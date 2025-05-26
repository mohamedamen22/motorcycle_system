<?php
error_reporting(E_ALL); // Muujin dhammaan qaladaadka si fiican u arag

// Isku xirka database-ka
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Hubinta haddii foomka la soo gudbiyay
if (isset($_POST['view'])) {
    $dateTaken = $_POST['dateTaken'];

    // SQL Query
    $query = "SELECT tblattendance.Id, tblattendance.status, tblattendance.dateTimeTaken, tblclass.className,
              tblclassarms.classArmName, tblsessionterm.sessionName, tblsessionterm.termId, tblterm.termName,
              tblstudents.firstName, tblstudents.lastName, tblstudents.otherName, tblstudents.admissionNumber
              FROM tblattendance
              INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
              INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
              INNER JOIN tblsessionterm ON tblsessionterm.Id = tblattendance.sessionTermId
              INNER JOIN tblterm ON tblterm.Id = tblsessionterm.termId
              INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
              WHERE tblattendance.dateTimeTaken = ? 
              AND tblattendance.classId = ? 
              AND tblattendance.classArmId = ?";

    // Tijaabinta haddii query-ga uu sax yahay
    if ($stmt = $conn->prepare($query)) {
        echo "<p>Query-ga si guul leh ayaa loo diyaariyay.</p>";

        // Bixinta Xogta
        $classId = $_SESSION['classId'];
        $classArmId = $_SESSION['classArmId'];

        // Bind parameters
        $stmt->bind_param("sss", $dateTaken, $classId, $classArmId);

        // Fulinta query-ga
        if ($stmt->execute()) {
            // Helidda natiijada
            $result = $stmt->get_result();

            // Hubi haddii wax natiijo ah la helay
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Qorista xogta
                    echo "<tr>";
                    echo "<td>{$row['firstName']}</td>";
                    echo "<td>{$row['lastName']}</td>";
                    echo "<td>{$row['otherName']}</td>";
                    echo "<td>{$row['admissionNumber']}</td>";
                    echo "<td>{$row['className']}</td>";
                    echo "<td>{$row['classArmName']}</td>";
                    echo "<td>{$row['sessionName']}</td>";
                    echo "<td>{$row['termName']}</td>";
                    echo "<td>{$row['status']}</td>";
                    echo "<td>{$row['dateTimeTaken']}</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No records found</td></tr>";
            }
        } else {
            // Haddii query-ga fulin waayo
            echo "<p>Error executing query: " . $stmt->error . "</p>";
        }

        // Xiridda statement-ka
        $stmt->close();
    } else {
        // Haddii prepare() uu guuldareysto
        echo "<p>Error preparing query: " . $conn->error . "</p>";
    }
}
?>
