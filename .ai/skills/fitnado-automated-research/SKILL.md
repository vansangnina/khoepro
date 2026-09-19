---
name: fitnado-automated-research
description: FITNADO Automated Product Research & AI Research Agent workflow guide
---

# FITNADO Automated Product Research & AI Research Agent Skill

## 1. Overview
The **Automated Product Research System** (Phase 04) transforms manual product discovery into a semi-automated, scalable pipeline. It allows discovery via multiple providers (AI Agent, TikTok/Shopee/Lazada crawler mocks, CSV import), queuing background discovery jobs, extracting market insights with AI, tracking evidence provenance, and evaluating candidates through an automated scoring engine.

---

## 2. Core Architecture

```text
Discovery Seeds (`table_product_research_seed`)
       ↓
Job Queue Dispatch (`table_product_research_job`)
       ↓
CLI / Cron Worker (`cron/product_research_worker.php`)
       ↓
Research Provider Discovery (`ResearchProviderInterface`)
       ↓
AI Research Agent (`AIResearchAgent` with schema validation)
       ↓
Normalization & Duplicate Detection (`ProductResearch::checkDuplicate`)
       ↓ (If duplicate: update `last_seen_at` & save Snapshot)
Automated Scoring Engine (`ProductResearch::calculateTotalScore`)
       ↓
Evidence Logging (`table_product_research_evidence`)
       ↓
Candidate Ingestion (`status = 'RESEARCHED'`)
       ↓
[STRICT HUMAN GATE]
       ↓
Admin Review & Approval (`status = 'APPROVED'`)
       ↓
Convert to Draft Product (`table_product`)
```

---

## 3. Key Classes and Components

- **`ResearchCandidateDTO`** (`libraries/class/class.ResearchProvider.php`):
  Unified normalized Data Transfer Object across all discovery providers.
- **`ResearchProviderInterface` / `ResearchProviderFactory`** (`libraries/class/class.ResearchProvider.php`):
  Extensible discovery factory supporting `ai_agent`, `tiktok`, `shopee`, `lazada`, `csv`, and `manual`.
- **`AIResearchAgent`** (`libraries/class/class.AIResearchAgent.php`):
  Multi-LLM client (Gemini 1.5 Flash, OpenAI-compatible, Mock fallback) enforcing prompt versioning (`research-v1.0`), structured JSON validation, and rate limiting.
- **`ResearchJobQueue`** (`libraries/class/class.ResearchJobQueue.php`):
  Transactional job manager with status transitions (`PENDING` -> `RUNNING` -> `SUCCESS` / `FAILED`), concurrency locking, and stale job auto-recovery (> 10 mins).
- **`product_research_worker.php`** (`cron/product_research_worker.php`):
  Background worker executable via CLI or token-protected HTTP endpoint.

---

## 4. Business & Safety Rules

1. **Strict Separation of FACT vs AI_ANALYSIS**:
   - AI NEVER invents numerical market metrics (`sales_count`, `rating`, `review_count`, `commission_rate`).
   - If not present in source platform data, metric fields MUST be `NULL`.
   - AI insights (angles, pain points, demo potential, confidence) are recorded as `evidence_type = 'AI_ANALYSIS'`.
2. **Strict Human Gate**:
   - Automated discovery stops at `status = 'RESEARCHED'`.
   - Automated jobs NEVER auto-approve (`APPROVED`) or auto-publish to `table_product`.
3. **Evidence Provenance & Snapshots**:
   - Every discovered signal is logged in `table_product_research_evidence`.
   - Subsequent scans of existing candidates update `last_seen_at` and log a metric history record in `table_product_research_snapshot`.
4. **Credential Security**:
   - API keys are stored in `table_setting.options` JSON (`ai_research_config`), masked when rendered in Admin UI (`sk-...1234`), and NEVER committed to version control.
