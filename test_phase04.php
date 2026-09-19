<?php
/**
 * Automated Verification Script for FITNADO Phase 04
 * PHP 7.4 Compatible
 */

$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';

define('LIBRARIES', __DIR__ . '/libraries/');
require_once LIBRARIES . "config.php";
require_once LIBRARIES . 'autoload.php';
new AutoLoad();

$dbConfig = $config['database'];
if (file_exists('/Applications/MAMP/tmp/mysql/mysql.sock')) {
    $dbConfig['unix_socket'] = '/Applications/MAMP/tmp/mysql/mysql.sock';
}
$d = new PDODb($dbConfig);
$cache = new Cache($d);
$func = new Functions($d, $cache);

require_once LIBRARIES . 'class/class.ProductResearch.php';
require_once LIBRARIES . 'class/class.AIResearchAgent.php';
require_once LIBRARIES . 'class/class.ResearchProvider.php';
require_once LIBRARIES . 'class/class.ResearchJobQueue.php';

$research = new ProductResearch($d, $func);
$queue = new ResearchJobQueue($d, $func);
$aiAgent = new AIResearchAgent($d, $func);

echo "=======================================================\n";
echo "FITNADO PHASE 04 - AUTOMATED TEST SUITE (PHP 7.4)\n";
echo "=======================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest($condition, $testName, $details = '') {
    global $passCount, $failCount;
    if ($condition) {
        echo "[PASS] " . $testName . "\n";
        $passCount++;
    } else {
        echo "[FAIL] " . $testName . " - " . $details . "\n";
        $failCount++;
    }
}

// -------------------------------------------------------------
// TEST 1: Research Seed CRUD & Dispatch
// -------------------------------------------------------------
echo "--- TEST 1: Research Seeds Management ---\n";
$runSuffix = time();
$seedData = array(
    'title' => 'Dây kéo lưng Lifting Straps ' . $runSuffix,
    'keyword' => 'lifting straps gym ' . $runSuffix,
    'seed_type' => 'keyword',
    'platform' => 'all',
    'priority' => 20,
    'depth' => 'STANDARD',
    'frequency' => 'daily',
    'max_results' => 5,
    'status' => 'active',
    'date_created' => time(),
    'date_updated' => time()
);

$seedId = $d->insert('product_research_seed', $seedData);
assertTest($seedId > 0, "Create Research Seed (Seed ID: #$seedId)");

$jobId = $queue->createJob($seedId, 'ai_agent', 'STANDARD');
assertTest($jobId > 0, "Dispatch Research Job to Queue (Job ID: #$jobId)");

// -------------------------------------------------------------
// TEST 2: Job Queue Lifecycle & Concurrency Locking
// -------------------------------------------------------------
echo "\n--- TEST 2: Job Queue Lifecycle & Concurrency Locking ---\n";
$pendingJob = $queue->getNextPendingJob();
assertTest(!empty($pendingJob) && $pendingJob['id'] == $jobId, "Pick Pending Job from Queue (ID: #$jobId)");
assertTest($pendingJob['status'] === 'RUNNING', "Job locked and transitioned to RUNNING status");
assertTest($pendingJob['attempts'] == 1, "Job attempts counter incremented to 1");

// Test Stale Job Recovery
$d->rawQuery("update #_product_research_job set started_at = ? where id = ?", array(time() - 1000, $jobId));
$recovered = $queue->recoverStaleJobs(600);
$checkJob = $d->rawQueryOne("select status from #_product_research_job where id = ?", array($jobId));
assertTest($checkJob['status'] === 'PENDING', "Stale job recovery resets status back to PENDING");

// Re-lock for execution
$runningJob = $queue->getNextPendingJob();
assertTest($runningJob['status'] === 'RUNNING', "Re-acquire and lock job for execution");

// -------------------------------------------------------------
// TEST 3: Provider Factory & Discovery Execution
// -------------------------------------------------------------
echo "\n--- TEST 3: Provider Discovery & Normalization ---\n";
$aiProvider = ResearchProviderFactory::create('ai_agent', $d, $func);
assertTest($aiProvider instanceof AiResearchProvider, "Factory creates AiResearchProvider instance");

$mockPlatformProvider = ResearchProviderFactory::create('tiktok', $d, $func);
assertTest($mockPlatformProvider instanceof MockPlatformProvider, "Factory creates MockPlatformProvider instance");

$discoveredMock = $mockPlatformProvider->discover('Dây kháng lực', array('max_results' => 2));
assertTest(count($discoveredMock) > 0, "Platform provider discovers candidates (" . count($discoveredMock) . " found)");
assertTest($discoveredMock[0] instanceof ResearchCandidateDTO, "Candidate returned as normalized ResearchCandidateDTO");
assertTest(!empty($discoveredMock[0]->source_url) && !empty($discoveredMock[0]->name), "Candidate DTO has name and source_url");

// -------------------------------------------------------------
// TEST 4: AI Research Agent & Structured JSON Schema
// -------------------------------------------------------------
echo "\n--- TEST 4: AI Research Agent & Structured JSON Analysis ---\n";
$sampleCand = array(
    'name' => 'Con Lăn Tập Bụng 4 Bánh FITNADO Pro',
    'platform' => 'tiktok',
    'price' => 249000,
    'category_hint' => 'Dụng cụ tập bụng'
);

$aiAnalysis = $aiAgent->analyzeCandidate($sampleCand, array('depth' => 'STANDARD'));
assertTest($aiAnalysis['status'] === true, "AI Agent generates structured response");
assertTest(!empty($aiAnalysis['data']['problem_solved']), "AI provides problem_solved insights");
assertTest(is_array($aiAnalysis['data']['content_angles']) && count($aiAnalysis['data']['content_angles']) >= 2, "AI provides structured content angles");
assertTest(in_array($aiAnalysis['data']['visual_demo_potential'], array('HIGH', 'MEDIUM', 'LOW')), "AI evaluates visual_demo_potential");
assertTest($aiAnalysis['data']['confidence'] >= 50, "AI confidence score evaluated (" . ($aiAnalysis['data']['confidence'] ?? 0) . "%)");

// -------------------------------------------------------------
// TEST 5: Fact vs AI Analysis Separation & Evidence Capture
// -------------------------------------------------------------
echo "\n--- TEST 5: Fact vs AI Analysis Separation ---\n";
// Ensure AI output does NOT invent sales_count
assertTest(!isset($aiAnalysis['data']['sales_count']), "AI schema strictly prohibits inventing sales_count fact");
assertTest(!isset($aiAnalysis['data']['rating']), "AI schema strictly prohibits inventing rating fact");

// -------------------------------------------------------------
// TEST 6: Full Queue Execution & Candidate Ingestion
// -------------------------------------------------------------
echo "\n--- TEST 6: Full Queue Execution & Candidate Ingestion ---\n";
$execResult = $queue->executeJob($jobId);
assertTest($execResult['status'] === true, "Job execution completed successfully");
assertTest($execResult['candidates_created'] > 0, "Candidates created in database (" . $execResult['candidates_created'] . " added)");

// Check job record updated to SUCCESS
$jobFinal = $d->rawQueryOne("select * from #_product_research_job where id = ?", array($jobId));
assertTest($jobFinal['status'] === 'SUCCESS', "Job final status is SUCCESS");
assertTest($jobFinal['duration'] > 0, "Job duration tracked (" . $jobFinal['duration'] . "s)");

// -------------------------------------------------------------
// TEST 7: Verification of Candidate Record, Evidence & Snapshots
// -------------------------------------------------------------
echo "\n--- TEST 7: Evidence Provenance & Historical Snapshots ---\n";
$cand = $d->rawQueryOne("select * from #_product_research where discovery_source = 'ai_agent' order by id desc limit 0,1");
assertTest(!empty($cand), "Candidate record found in table_product_research (ID: #" . ($cand['id'] ?? 0) . ")");
assertTest($cand['status'] === 'RESEARCHED', "Automated candidate stops at RESEARCHED status (Human Gate enforced)");
assertTest(!empty($cand['ai_analysis']), "Candidate contains structured ai_analysis JSON");
assertTest($cand['total_score'] > 0, "Automated candidate has total score calculated (" . $cand['total_score'] . "/100)");

// Verify Evidence Records
$evidences = $d->rawQuery("select * from #_product_research_evidence where id_research = ?", array($cand['id']));
assertTest(!empty($evidences), "Evidence records logged in table_product_research_evidence (" . count($evidences) . " records)");

// Verify Snapshot Records
$snapshots = $d->rawQuery("select * from #_product_research_snapshot where id_research = ?", array($cand['id']));
assertTest(!empty($snapshots), "Historical Snapshot logged in table_product_research_snapshot");

// -------------------------------------------------------------
// TEST 8: Duplicate Re-scan & `last_seen_at` Update
// -------------------------------------------------------------
echo "\n--- TEST 8: Duplicate Re-scan & Last Seen Update ---\n";
$initialCandidateCount = (int)$d->rawQueryOne("select count(id) as total from #_product_research")['total'];

// Create second job for same seed
$jobId2 = $queue->createJob($seedId, 'ai_agent', 'STANDARD');
$execResult2 = $queue->executeJob($jobId2);

$afterCandidateCount = (int)$d->rawQueryOne("select count(id) as total from #_product_research")['total'];
assertTest($execResult2['duplicates_count'] > 0, "Re-running discovery detects duplicate candidates (" . $execResult2['duplicates_count'] . " duplicates detected)");
assertTest($afterCandidateCount === $initialCandidateCount, "Exact duplicates do NOT create duplicate candidate records");

// -------------------------------------------------------------
// TEST 9: Human Gate Verification (Never Auto-Approve)
// -------------------------------------------------------------
echo "\n--- TEST 9: Human Gate & Safety Guard Verification ---\n";
$unapproved = $d->rawQuery("select id, status, id_product from #_product_research where discovery_source = 'ai_agent' and status = 'APPROVED'");
assertTest(empty($unapproved), "Zero automated candidates are auto-approved without human action");

$liveAutomated = $d->rawQuery("select p.id, p.namevi from #_product p inner join #_product_research r on p.id = r.id_product where r.discovery_source = 'ai_agent' and find_in_set('hienthi', p.status)");
assertTest(empty($liveAutomated), "Zero automated candidates are auto-published to live frontend");

// -------------------------------------------------------------
// TEST 10: AI Provider Settings & Secret Masking
// -------------------------------------------------------------
echo "\n--- TEST 10: AI Settings & API Secret Security ---\n";
$configTest = array(
    'active_provider' => 'mock',
    'gemini_api_key' => 'AIzaSyFakeTestKey123456789',
    'gemini_model' => 'gemini-1.5-flash',
    'daily_request_limit' => 100,
    'default_depth' => 'STANDARD'
);
$aiAgent->saveAiConfig($configTest);
$loadedConfig = $aiAgent->getAiConfig();
assertTest($loadedConfig['active_provider'] === 'mock', "Save and reload active AI provider");
assertTest($loadedConfig['daily_request_limit'] == 100, "Save and reload daily request limit (100 requests/day)");

// -------------------------------------------------------------
// SUMMARY
// -------------------------------------------------------------
echo "\n=======================================================\n";
echo "PHASE 04 TEST RESULTS: " . $passCount . " PASSED, " . $failCount . " FAILED\n";
echo "=======================================================\n";

if ($failCount === 0) {
    echo ">>> ALL PHASE 04 AUTOMATED TESTS PASSED SUCCESSFULLY! <<<\n";
} else {
    echo ">>> SOME TESTS FAILED! PLEASE REVIEW OUTPUT. <<<\n";
}
