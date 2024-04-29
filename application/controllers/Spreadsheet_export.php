<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');
require FCPATH.'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class Spreadsheet_export extends CI_Controller {
	function __construct (){
        parent::__construct();
        $this->load->model('Altt_model');
    }


   public function exportKarmaLog(){
      $row_no = $this->input->get('page_no');
      $row_per_page = $this->input->get('num_sort');
      $select_sort = $this->input->get('select_sort');
      $type = $this->input->get('type');
      $from = $this->input->get('from');
      $to = $this->input->get('to');

      // Row position
      if($row_no != 0){
            $row_no = ($row_no-1) * $row_per_page;
      }

      $data = $this->Altt_model->getKarmaLogDataSortExport($row_per_page, $row_no);
      $karma_data = $data['karma_log'];

      $spreadsheet = new Spreadsheet();
      $sheet = $spreadsheet->getActiveSheet();

      if ($select_sort == 'default'){
         $sheet->setCellValue('A1', 'Altcoinstalks Karma Logs');
         $sheet->setCellValue('A2', 'Username');
         $sheet->setCellValue('B2', 'Position');
         $sheet->setCellValue('C2', 'Karma Point');
         $sheet->setCellValue('D2', 'Total Karma');
         $sheet->setCellValue('E2', 'Datetime');
       
         $sr=3;
         foreach ($karma_data as $kd) {
            $sheet->setCellValue('A'.$sr, $kd['username']);
            $sheet->setCellValue('B'.$sr, $kd['position']);
            $sheet->setCellValue('C'.$sr, $kd['karma']);
            $sheet->setCellValue('D'.$sr, $kd['total_karma']);
            $sheet->setCellValue('E'.$sr, $kd['created_at']);
            $sr++;
         }

      }
      else if ($select_sort == 'highest_karma_all_time'){
         $sheet->setCellValue('A1', 'Altcoinstalks All-time High Karma Earner');
         $sheet->setCellValue('A2', 'Username');
         $sheet->setCellValue('B2', 'Position');
         $sheet->setCellValue('C2', 'Total Karma');

         $sr=3; 
         foreach ($karma_data as $kd) {
            $sheet->setCellValue('A'.$sr, $kd['username']);
            $sheet->setCellValue('B'.$sr, $kd['position']);
            $sheet->setCellValue('C'.$sr, $kd['total_karma']);
            $sr++;
         }
      }
      else if ($select_sort == 'karma_30_days'  || $select_sort == 'karma_60_days' || $select_sort == 'karma_90_days' || $select_sort == 'karma_120_days'){
         $sheet->setCellValue('A1', str_replace('_', ' ', $select_sort));

         $sheet->setCellValue('A2', 'Username');
         $sheet->setCellValue('B2', 'Position');
         $sheet->setCellValue('C2', 'Karma Point');
         $sheet->setCellValue('D2', 'Total Karma');

         $sr=3; 
         foreach ($karma_data as $kd) {
            $sheet->setCellValue('A'.$sr, $kd['username']);
            $sheet->setCellValue('B'.$sr, $kd['position']);
            $sheet->setCellValue('C'.$sr, $kd['karma']);
            $sheet->setCellValue('D'.$sr, $kd['total_karma']);
            $sr++;
         }
      }
      else if ($select_sort == 'highest_karma_today'){
         $sheet->setCellValue('A1', 'Altcoinstalks Highest Karma Earner Today'. date('Y-m-d'));
         $sheet->setCellValue('A2', 'Username');
         $sheet->setCellValue('B2', 'Position');
         $sheet->setCellValue('C2', 'Karma Point');
         $sheet->setCellValue('D2', 'Total Karma');

         $sr=3; 
         foreach ($karma_data as $kd) {
            $sheet->setCellValue('A'.$sr, $kd['username']);
            $sheet->setCellValue('B'.$sr, $kd['position']);
            $sheet->setCellValue('C'.$sr, $kd['karma']);
            $sheet->setCellValue('D'.$sr, $kd['total_karma']);
            $sr++;
         }
      }
      else if ($select_sort == 'highest_karma_this_month'){
         $sheet->setCellValue('A1', 'Altcoinstalks Highest Karma Earner('.date('M').')');
         $sheet->setCellValue('A2', 'Username');
         $sheet->setCellValue('B2', 'Position');
         $sheet->setCellValue('C2', 'Karma Point');
         $sheet->setCellValue('D2', 'Total Karma');

         $sr=3; 
         foreach ($karma_data as $kd) {
            $sheet->setCellValue('A'.$sr, $kd['username']);
            $sheet->setCellValue('B'.$sr, $kd['position']);
            $sheet->setCellValue('C'.$sr, $kd['karma']);
            $sheet->setCellValue('D'.$sr, $kd['total_karma']);
            $sr++;
         }
      }
      else if ($select_sort == 'custom'){
         $sheet->setCellValue('A1', 'Altcoinstalks Karma Logs ('. $from.' - '.$to.')' );
         $sheet->setCellValue('A2', 'Username');
         $sheet->setCellValue('B2', 'Position');
         $sheet->setCellValue('C2', 'Karma Point');
         $sheet->setCellValue('D2', 'Total Karma');

         $sr=3; 
         foreach ($karma_data as $kd) {
            $sheet->setCellValue('A'.$sr, $kd['username']);
            $sheet->setCellValue('B'.$sr, $kd['position']);
            $sheet->setCellValue('C'.$sr, $kd['karma']);
            $sheet->setCellValue('D'.$sr, $kd['total_karma']);
            $sr++;
         }
      }

      
      if($type == 'csv'){
         header("Content-type: application/csv");
         header('Content-Disposition: attachment;filename="altcoinstalks_karmalogs_'.time().'.csv"');
         $writer = new Csv($spreadsheet);
      }
      else if ($type == 'excel'){
         header('Content-Type: application/vnd.ms-excel');
         header('Content-Disposition: attachment;filename="altcoinstalks_karmalogs_'.time().'.xlsx"');
         $writer = new Xlsx($spreadsheet);
      }
      
      $writer->save("php://output");
   }

}