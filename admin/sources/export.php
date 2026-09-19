<?php
if (!defined('SOURCES')) die("Error");

/* Kiểm tra active export */
if (isset($config['product'])) {
    $arrCheck = array();
    foreach ($config['product'] as $k => $v) if (isset($v['export']) && $v['export'] == true) $arrCheck[] = $k;
    if (!count($arrCheck) || !in_array($type, $arrCheck)) $func->transfer(trangkhongtontai, "index.php", false);
} else {
    $func->transfer(trangkhongtontai, "index.php", false);
}

switch ($act) {
    case "man":
        $template = "export/man/mans";
        break;

    case "exportExcel":
        exportExcel();
        break;

    default:
        $template = "404";
}

function exportExcel()
{
    global $d, $langadmin, $func, $type;

    /* Setting */
    $setting = $d->rawQueryOne("select * from #_setting limit 0,1");
    $optsetting = (isset($setting['options']) && $setting['options'] != '') ? json_decode($setting['options'], true) : null;

    $danhmuc = $d->rawQuery("select * from #_product_list where type=? order by numb asc",array('san-pham'));
    

    if (isset($_POST['exportExcel'])) {
        /* PHPExcel */
        require_once LIBRARIES . 'PHPExcel.php';

        /* Khởi tọa đối tượng */
        $PHPExcel = new PHPExcel();

        
        

        /* Khởi tạo thông tin người tạo */
        $PHPExcel->getProperties()->setCreator($setting['namevi']);
        $PHPExcel->getProperties()->setLastModifiedBy($setting['namevi']);
        $PHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $PHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $PHPExcel->getProperties()->setDescription("Document for Office 2007 XLSX, generated using PHP classes.");




        /* Khởi tạo mảng column */
        $alphas = range('A', 'Z');
        $array_columns = array(
            'numb' => 'STT',
            'id_list' => danhmuccap1,
            'id_cat' => danhmuccap2,
            'code' => masanpham,
            'namevi' => sanpham,
            'regular_price' => gia,
            'sale_price' => giamoi,
            'discount' => chieckhau,
            'descvi' => mota,
            'contentvi' => noidung,
            'photo' => hinhanh
        );
        $styleArray = array(
            'font' => array('color' => array('rgb' => 'ffffff'), 'name' => 'Calibri', 'bold' => true, 'italic' => false, 'size' => 10),
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '007BFF'))
        );
        /* Khởi tạo và style cho row đầu tiên */
        $i = 0;
        foreach ($array_columns as $k => $v) {
            if ($k == 'numb') {
                $PHPExcel->getActiveSheet()->getColumnDimension($alphas[$i])->setWidth(5);
            } else if ($k == 'id_list' || $k == 'id_cat') {
                $PHPExcel->getActiveSheet()->getColumnDimension($alphas[$i])->setWidth(20);
            } else if ($k == 'code') {
                $PHPExcel->getActiveSheet()->getColumnDimension($alphas[$i])->setWidth(15);
            } else if ($k == 'namevi') {
                $PHPExcel->getActiveSheet()->getColumnDimension($alphas[$i])->setWidth(40);
            } else if ($k == 'regular_price' || $k == 'sale_price' || $k == 'discount') {
                $PHPExcel->getActiveSheet()->getColumnDimension($alphas[$i])->setWidth(10);
            } else if ($k == 'descvi' || $k == 'contentvi' || $k == 'photo') {
                $PHPExcel->getActiveSheet()->getColumnDimension($alphas[$i])->setWidth(35);
            }

            $PHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$i] . '1', $v);
            $PHPExcel->getActiveSheet()->getStyle($alphas[$i] . '1')->applyFromArray(array('font' => array('color' => array('rgb' => 'ffffff'), 'name' => 'Calibri', 'bold' => true, 'italic' => false, 'size' => 10), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '007BFF'))));

            $i++;
        }
        /* Lấy và Xuất dữ liệu */
        $whereCategory = "";
        $data = (isset($_POST['data'])) ? $_POST['data'] : null;
        if ($data) {
            foreach ($data as $column => $value) {
                if ($value > 0) {
                    $whereCategory .= " and " . $column . " = " . $value;
                }
            }
        }

        $product = $d->rawQuery("select * from #_product where type = ? $whereCategory order by numb,id desc", array($type));

        foreach ($danhmuc as $k_danhmuc => $v_danhmuc) {
            $product = $d->rawQuery("select * from #_product where type = ? and id_list=? order by numb,id desc", array($type,$v_danhmuc['id']));
            if($k_danhmuc == 0){
                $position = 2;
                for ($i = 0; $i < count($product); $i++) {
                    $j = 0;
                    foreach ($array_columns as $k => $v) {
                        if ($k == 'id_list') {
                            $namelist = $d->rawQueryOne("select namevi from #_product_list where id = ? limit 0,1", array($product[$i][$k]));
                            $datacell = (!empty($namelist['namevi'])) ? $namelist['namevi'] : '';
                        } else if ($k == 'id_cat') {
                            $namecat = $d->rawQueryOne("select namevi from #_product_cat where id = ? limit 0,1", array($product[$i][$k]));
                            $datacell = (!empty($namecat['namevi'])) ? $namecat['namevi'] : '';
                        } else {
                            $datacell = $product[$i][$k];
                        }

                        if ($k == 'code') {
                            $PHPExcel->getActiveSheet()->setCellValueExplicit($alphas[$j] . $position, $func->decodeHtmlChars($datacell),  PHPExcel_Cell_DataType::TYPE_STRING);
                        } else {
                            $PHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$j] . $position, $func->decodeHtmlChars($datacell));
                        }
                        $j++;
                    }
                    $position++;
                }
                /* Style cho các row dữ liệu */
                $position = 2;
                for ($i = 0; $i < count($product); $i++) {
                    $j = 0;
                    foreach ($array_columns as $k => $v) {
                        $PHPExcel->getActiveSheet()->getStyle($alphas[$j] . $position)->applyFromArray(
                            array(
                                'font' => array(
                                    'color' => array('rgb' => '000000'),
                                    'name' => 'Calibri',
                                    'bold' => false,
                                    'italic' => false,
                                    'size' => 10
                                ),
                                'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                    'wrap' => true
                                )
                            )
                        );
                        $j++;
                    }
                    $position++;
                }
            }else{
                $sheet = $PHPExcel->getActiveSheet(); // Lấy sheet ban đầu
                $objPHPExcelClone = clone $PHPExcel;
                // Xoá dữ liệu trên sheet sao chép (giữ lại định dạng)
                $sheetClone = $objPHPExcelClone->getActiveSheet();
                $cellIterator = $sheetClone->getRowIterator()->current()->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false); // Đảm bảo xoá tất cả các ô, bao gồm các ô trống
                foreach ($cellIterator as $cell) {
                    $cell->setValue(''); // Xoá giá trị trong từng ô
                }

                $inew = 0;
                foreach ($array_columns as $k_new => $v_new) {
                    $sheetClone->setCellValue($alphas[$inew] . '1', $v_new);
                    $inew++;
                }

                $position = 2;
                for ($i = 0; $i < count($product); $i++) {
                    $j = 0;
                    foreach ($array_columns as $k => $v) {
                        if ($k == 'id_list') {
                            $namelist = $d->rawQueryOne("select namevi from #_product_list where id = ? limit 0,1", array($product[$i][$k]));
                            $datacell = (!empty($namelist['namevi'])) ? $namelist['namevi'] : '';
                        } else if ($k == 'id_cat') {
                            $namecat = $d->rawQueryOne("select namevi from #_product_cat where id = ? limit 0,1", array($product[$i][$k]));
                            $datacell = (!empty($namecat['namevi'])) ? $namecat['namevi'] : '';
                        } else {
                            $datacell = $product[$i][$k];
                        }

                        if ($k == 'code') {
                            $sheetClone->setCellValueExplicit($alphas[$j] . $position, $func->decodeHtmlChars($datacell),  PHPExcel_Cell_DataType::TYPE_STRING);
                        } else {
                            $sheetClone->setCellValue($alphas[$j] . $position, $func->decodeHtmlChars($datacell));
                        }
                        $j++;
                    }
                    $position++;
                }
                /* Style cho các row dữ liệu */
                $position = 2;
                for ($i = 0; $i < count($product); $i++) {
                    $j = 0;
                    foreach ($array_columns as $k => $v) {
                        $sheetClone->getStyle($alphas[$j] . $position)->applyFromArray(
                            array(
                                'font' => array(
                                    'color' => array('rgb' => '000000'),
                                    'name' => 'Calibri',
                                    'bold' => false,
                                    'italic' => false,
                                    'size' => 10
                                ),
                                'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                    'wrap' => true
                                )
                            )
                        );
                        $j++;
                    }
                    $position++;
                }
                $sheetClone->setTitle($v_danhmuc['namevi']);
                $PHPExcel->addSheet($sheetClone);
                
            }
        }

        /* Rename title */
        $PHPExcel->getActiveSheet()->setTitle($danhmuc[0]['namevi']);

        /* Khởi tạo chỉ mục ở đầu sheet */
        $PHPExcel->setActiveSheetIndex(0);

        /* Xuất file */
        $time = time();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="products_' . $time . '_' . date('d_m_Y') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    } else {
        $func->transfer(dulieurong, "index.php?com=export&act=man&type=" . $type, false);
    }
}
