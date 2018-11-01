<?php

date_default_timezone_set("America/Cancun");
use Phalcon\Http\Response;

class ReportesController extends \Phalcon\Mvc\Controller
{
    public function initialize()
    {
        session_start();
        $this->view->setTemplateBefore('menu');
    }

    public function indexAction()
    {
        if(!$this->session->has("user"))
        {
           $response = new Response();
           $response->redirect('/Reservaciones');
           return $response;
        }
    }

    public function crearReporAction($res, $tipo)
    {
        $mesa = Reservaciones::reportes($res, date('Y-m-d'), $tipo);
        $mesas = [];

       foreach ($mesa as $key => $value) 
        {
            $obj = new stdClass();
            $obj->reserva = $value->getId();
            $obj->hora = $value->getHora();
            $obj->fecha = $value->getFecha();
            $obj->id = $value->getNumero();
            $obj->capacidad = $value->getCapacidad();
            $obj->cuarto = $value->getCuarto();
            $obj->folio = $value->getFolio();
            $obj->nombre = $value->getNombre();
            $obj->notas = $value->getComentarios();
            $obj->operador = $value->getNombreOpe();
            $obj->fechareserva = $value->getFechaReserva();

            array_push($mesas, $obj);
        }

        $datos = array('data' => $mesas);

        $response = new Response();
        $response->setContent(json_encode($datos));
        return $response;
    }

    public function crearExcelAction($res, $tipo)
    {
        $this->view->disable();
        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(10); 
        $objPHPExcel->getActiveSheet()->getStyle("A1:J1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
        $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->applyFromArray($styleArray);
        unset($styleArray);
        $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
        $objPHPExcel->getActiveSheet()->getStyle("A1:J1")->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(65);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(35);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Confirmacion');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Hora');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Fecha');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Mesa');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Capacidad');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Habitacion');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', '# Folio');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Nombre');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'Notas');
        $objPHPExcel->getActiveSheet()->setCellValue('J1', 'Operador');

        $mesa = Reservaciones::reportes($res, date('Y-m-d'), $tipo);
        $i = 2;

       foreach ($mesa as $value) 
        {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $value->getId());
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $value->getHora());
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $value->getFecha());
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.$i, $value->getNumero());
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $value->getCapacidad());
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $value->getCuarto());
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('G'.$i, $value->getFolio());
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $value->getNombre());
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $value->getComentarios());
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $value->getNombreOpe());
            $i++;
        }
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('C0C0C0C0');
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Total: ' . ($i - 2));

        $objPHPExcel->getActiveSheet()->setTitle('Hoja1');
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte.xls"');
        header('Cache-Control: max-age=0');
        $objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        $objWriter->save('php://output');
    }
}

