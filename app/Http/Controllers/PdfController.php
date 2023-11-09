<?php

namespace App\Http\Controllers;

use chillerlan\QRCode\QRCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use TCPDF;
use setasign\Fpdi\Tcpdf\Fpdi;

class PdfController extends Controller
{
    //este es el que recibe el pf
    public function uploadPDF(Request $request)
    {
        if ($request->hasFile('pdf_file') && $request->file('pdf_file')->isValid()) {
            $pdfFile = $request->file('pdf_file');

            $tempPdfPath = $pdfFile->storeAs('temp', 'uploaded_pdf.pdf');

            $pdf = new Fpdi();
            $pdf->setSourceFile(storage_path('app/' . $tempPdfPath));
            $pageNumber = $pdf->ImportPage(1);
            $pdf->AddPage('L');
            $pdf->useTemplate($pageNumber);

            $qrText = 'https://www.ejemplo.com';
            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->margin(2)->generate($qrText);

            $tempFilename = 'qrcode_' . uniqid() . '.png';
            Storage::disk('local')->put($tempFilename, $qrCode);
            $tempFile = storage_path('app/' . $tempFilename);

            // Ajustar la posición del código QR
            $pdf->Image($tempFile, $pdf->GetPageWidth() - 55, 25, 30, 30);

            $pdfWithQRPath = storage_path('app/output_with_qr.pdf');
            $pdf->Output($pdfWithQRPath, 'F');

            Storage::disk('local')->delete($tempFilename);
            Storage::delete($tempPdfPath);

            return response()->download($pdfWithQRPath)->deleteFileAfterSend(true);
        }

        // Handle if no valid file was uploaded
        return "No valid PDF uploaded";
    }

    //este no trabaja con una view
    //sin instalar eso de modificar imagenes
     // Con SimpleSoftwareIO Genero un pdf en blanco y le metio el qr.
    public function generatePDFOk()
    {
         // Crear instancia de TCPDF
         $pdf = new TCPDF();

         // Agregar una nueva página
         $pdf->AddPage();

         // Crear instancia de QRCode
         $qrCode = new QRCode();

         // Texto para el código QR
         $qrText = 'https://www.ejemplo.com';

            // Generar el código QR como una imagen en formato PNG
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($qrText);

        // Almacenar el código QR como un archivo en el almacenamiento de Laravel
        $tempFilename = 'qrcode_' . uniqid() . '.png';
        Storage::disk('local')->put($tempFilename, $qrCode);

        // Obtener la ruta de la imagen del código QR
        $tempFile = storage_path('app/' . $tempFilename);

        // Insertar la imagen QR en el PDF usando la ruta relativa al almacenamiento
        $pdf->Image($tempFile, 50, 50, 100, 100);

        // Ruta para guardar el archivo PDF
        $pdfPath = storage_path('app/output.pdf');

        // Salida del PDF
        $pdf->Output($pdfPath, 'F');

        // Eliminar el archivo temporal
        Storage::disk('local')->delete($tempFilename);

        // Devolver el archivo PDF como descarga
        return response()->download($pdfPath)->deleteFileAfterSend(true);
    }

    //esto no generaba bien el qr porque el pdf quedaba en blanco
    // Con QRCode
    public function generatePDF2()
    {
        // Crear instancia de TCPDF
        $pdf = new TCPDF();

        // Agregar una nueva página
        $pdf->AddPage();

        // Crear instancia de QRCode
        $qrCode = new QRCode();

        // Texto para el código QR
        $qrText = 'https://www.ejemplo.com';

        // Generar el código QR como una imagen
        $qrImage = $qrCode->render($qrText, 200);

        // Almacenar el código QR como un archivo en el almacenamiento de Laravel
        $tempFilename = 'qrcode_' . uniqid() . '.png';
        Storage::disk('local')->put($tempFilename, $qrImage);

        // Obtener la ruta de la imagen del código QR
        $tempFile = storage_path('app/' . $tempFilename);

        // Insertar la imagen QR en el PDF usando la ruta relativa al almacenamiento
        $pdf->Image($tempFile, 50, 50, 100, 100);

        // Ruta para guardar el archivo PDF
        $pdfPath = storage_path('app/output.pdf');

        // Salida del PDF
        $pdf->Output($pdfPath, 'F');

        // Eliminar el archivo temporal
        Storage::disk('local')->delete($tempFilename);

        // Devolver el archivo PDF como descarga
        return response()->download($pdfPath)->deleteFileAfterSend(true);
    }

    //esto no generaba bien el qr porque el pdf quedaba en blanco
    public function generatePDFTomi()
    {
        // Crear instancia de TCPDF
        $pdf = new TCPDF();
        // Agregar una nueva página
        $pdf->AddPage();

        // Crear instancia de QRCode
        $qrCode = new QRCode();
        // Texto para el código QR
        $qrText = 'https://www.ejemplo.com';
        // Generar el código QR como una imagen (puedes ajustar el tamaño según tus necesidades)
        $qrImage = $qrCode->render($qrText, 200);

        // Obtener la ubicación del archivo temporal
        $tempFile = tempnam(sys_get_temp_dir(), 'qrcode_');
        imagepng($qrImage, $tempFile);

        // Insertar la imagen QR en el PDF
        $pdf->Image($tempFile, 50, 50, 100, 100);

        // Salida del PDF
        $pdf->Output('output.pdf', 'D');

        // Eliminar el archivo temporal
        unlink($tempFile);

        return response()->download('output.pdf')->deleteFileAfterSend(true);
    }
}
