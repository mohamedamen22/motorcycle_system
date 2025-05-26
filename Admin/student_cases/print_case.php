<?php
require('fpdf/fpdf.php');
require_once '../Includes/dbcon.php';
require_once '../Includes/session.php';

if (!isset($_GET['id'])) {
    die("No student ID provided");
}

$admissionNumber = $_GET['id'];

// Fetch student details
$studentQuery = "SELECT s.firstName, s.lastName, s.admissionNumber, 
                c.className, ca.classArmName 
                FROM tblstudents s
                INNER JOIN tblclass c ON s.classId = c.Id
                INNER JOIN tblclassarms ca ON s.classArmId = ca.Id
                WHERE s.admissionNumber = '$admissionNumber'";
$studentResult = $conn->query($studentQuery);
$student = $studentResult->fetch_assoc();

if (!$student) {
    die("Student not found");
}

// Fetch attendance records
$attendanceQuery = "SELECT a.status, a.dateTimeTaken 
                   FROM tblattendance a
                   WHERE a.admissionNo = '$admissionNumber'
                   ORDER BY a.dateTimeTaken DESC";
$attendanceResult = $conn->query($attendanceQuery);

// Create PDF with styling
class PDF extends FPDF {
    // Page header
    function Header() {
        // Logo
        $this->Image('../img/logo.png', 10, 10, 30);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 18);
        // Move to the right
        $this->Cell(80);
        // Title
        $this->Cell(30, 10, 'STUDENT ATTENDANCE REPORT', 0, 0, 'C');
        // Line break
        $this->Ln(20);
    }
    
    // Page footer
    function Footer() {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
    
    // Colored table header
    function ColoredHeader($w, $h, $txt, $border, $ln, $align, $fill) {
        $this->SetFillColor(59, 89, 152); // Dark blue
        $this->SetTextColor(255, 255, 255); // White
        $this->Cell($w, $h, $txt, $border, $ln, $align, $fill);
        $this->SetTextColor(0, 0, 0); // Reset to black
    }
}

// Create new PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// School information
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'School Name', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 7, 'Student Attendance Records', 0, 1, 'C');
$pdf->Ln(10);

// Student information section
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(59, 89, 152); // Dark blue
$pdf->SetTextColor(255, 255, 255); // White
$pdf->Cell(0, 10, 'Student Information', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0); // Black
$pdf->SetFont('Arial', '', 12);

$pdf->Cell(40, 8, 'Full Name:', 0, 0);
$pdf->Cell(0, 8, $student['firstName'] . ' ' . $student['lastName'], 0, 1);
$pdf->Cell(40, 8, 'Admission No:', 0, 0);
$pdf->Cell(0, 8, $student['admissionNumber'], 0, 1);
$pdf->Cell(40, 8, 'Class:', 0, 0);
$pdf->Cell(0, 8, $student['className'] . ' ' . $student['classArmName'], 0, 1);
$pdf->Ln(8);

// Attendance records table
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(59, 89, 152); // Dark blue
$pdf->SetTextColor(255, 255, 255); // White
$pdf->Cell(0, 10, 'Attendance Records', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0); // Black

// Table header
$pdf->SetFont('Arial', 'B', 12);
$pdf->ColoredHeader(80, 10, 'Date', 1, 0, 'L', true);
$pdf->ColoredHeader(0, 10, 'Status', 1, 1, 'L', true);
$pdf->SetFont('Arial', '', 12);

// Initialize counters
$totalPresent = 0;
$totalAbsent = 0;
$totalRecords = 0;

// Table rows
while ($row = $attendanceResult->fetch_assoc()) {
    $status = ($row['status'] == '1') ? 'Present' : 'Absent';
    $statusColor = ($row['status'] == '1') ? array(76, 175, 80) : array(244, 67, 54); // Green or Red
    
    $pdf->Cell(80, 8, date('F j, Y', strtotime($row['dateTimeTaken'])), 1, 0);
    
    // Set color based on status
    $pdf->SetTextColor($statusColor[0], $statusColor[1], $statusColor[2]);
    $pdf->Cell(0, 8, $status, 1, 1);
    $pdf->SetTextColor(0, 0, 0); // Reset to black
    
    // Update counters
    if ($row['status'] == '1') $totalPresent++;
    else $totalAbsent++;
    $totalRecords++;
}

// Summary section
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(59, 89, 152); // Dark blue
$pdf->SetTextColor(255, 255, 255); // White
$pdf->Cell(0, 10, 'Attendance Summary', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0); // Black
$pdf->SetFont('Arial', '', 12);

$pdf->Cell(60, 8, 'Total Records:', 0, 0);
$pdf->Cell(0, 8, $totalRecords, 0, 1);

$pdf->Cell(60, 8, 'Present:', 0, 0);
$pdf->SetTextColor(76, 175, 80); // Green
$pdf->Cell(0, 8, $totalPresent, 0, 1);
$pdf->SetTextColor(0, 0, 0); // Black

$pdf->Cell(60, 8, 'Absent:', 0, 0);
$pdf->SetTextColor(244, 67, 54); // Red
$pdf->Cell(0, 8, $totalAbsent, 0, 1);
$pdf->SetTextColor(0, 0, 0); // Black

$pdf->Cell(60, 8, 'Attendance Rate:', 0, 0);
$attendanceRate = ($totalRecords > 0) ? round(($totalPresent / $totalRecords) * 100, 2) : 0;
$pdf->Cell(0, 8, $attendanceRate . '%', 0, 1);
$pdf->Ln(10);

// Generated timestamp
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, 'Report generated on: ' . date('F j, Y \a\t h:i A'), 0, 0, 'R');

// Output PDF
$pdf->Output('I', 'Attendance_Report_' . $admissionNumber . '.pdf');
?>