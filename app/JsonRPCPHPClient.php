<?php

namespace Students;

use Exception;

class JsonRPCPHPClient {
    private string $urlServ;

    public function urlCall($body) {
        $options = array(
            CURLOPT_URL => $this->urlServ,
            CURLOPT_HEADER => false,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $body
        );
        try {
            $conn = curl_init();
            curl_setopt_array($conn, $options);
            $response = trim(curl_exec($conn));
            $response = json_decode($response, true);
            curl_close($conn);
            return $response;
        } catch (Exception $e) {
            return strval($e);
        }
    }

    public function __construct($urlServ) {
        $this->urlServ = $urlServ;
    }

    public function run() {
        $this->cleanupDatabase();
        $this->createData();
        $this->getData();
        $this->updateData();
        $this->getData();
        $this->removeData();
        $this->getData();
    }

    private function cleanupDatabase() {
        echo "Cleaning the DB ... </br>";
        $req = '{"id": "0", "jsonrpc": "2.0", "method": "cleanupDatabase", "params": {}}';
        $this->urlCall($req);
    }

    private function createData() {
        $this->createStudents();
        $this->createSchoolObjects();
        $this->createGrades();
    }

    private function getData() {
        $this->getStudents();
        $this->getSchoolObjects();
        $this->getGrades();
    }

    private function updateData() {
        echo "Updating data .. </br>";
        $this->updateStudents();
        $this->updateSchoolObjects();
        $this->updateGrades();
    }

    private function removeData() {
        echo "Deleting data ... </br>";
        $this->deleteStudents();
        $this->deleteSchoolObjects();
    }

    private function getGrades() {
        echo "Getting grades ... </br>";
        $this->getGradesForStudent("alex");
        $this->getGradesForStudent("iulia");
        $this->getGradesForSchoolObject("mate");
        $this->getGradesForSchoolObject("romana");
        $this->getGradesForSchoolObject("info");
        $this->getGradesForSchoolObject("fizica");
    }

    private function getSchoolObjects() {
        echo "Getting school objects ... </br>";
        $req = '{"id": "6", "jsonrpc": "2.0", "method": "getAllSchoolObjects", "params": {}}';
        $resp = $this->urlCall($req);
        $this->showTable($resp["result"], ["name", "teacher"]);
    }

    private function getStudents() {
        echo "Getting students ... </br>";
        $req = '{"id": "4", "jsonrpc": "2.0", "method": "getAllStudents", "params": {}}';
        $resp = $this->urlCall($req);
        $this->showTable($resp["result"], ["name", "group"]);
    }

    private function createGrades() {
        echo "Creating grades ...</br>";
        $this->createGradeForStudentAndSchoolObject("alex", "mate", 10);
        $this->createGradeForStudentAndSchoolObject("alex", "romana", 7);
        $this->createGradeForStudentAndSchoolObject("alex", "info", 3);
        $this->createGradeForStudentAndSchoolObject("alex", "fizica", 10);
        $this->createGradeForStudentAndSchoolObject("iulia", "mate", 10);
        $this->createGradeForStudentAndSchoolObject("iulia", "romana", 10);
        $this->createGradeForStudentAndSchoolObject("iulia", "fizica", 10);
        $this->createGradeForStudentAndSchoolObject("iulia", "info", 5);
    }

    private function createGradeForStudentAndSchoolObject(string $student, string $schoolObject, int $value) {
        $req = '{"id": "3", "jsonrpc": "2.0", "method": "addGrade", "params": 
            {"grade": { 
                    "student": { "name": "' . $student . '" }, 
                    "schoolObject": { "name": "' . $schoolObject . '" },
                    "value": "' . $value . '"
                }
            }
        }';
        $this->urlCall($req);
    }

    private function createSchoolObjects() {
        echo "Creating school objects ...</br>";
        $this->createSchoolObject("mate", "Ionescu");
        $this->createSchoolObject("romana", "Pantea");
        $this->createSchoolObject("info", "Gligor");
        $this->createSchoolObject("fizica", "Galis");
    }

    private function createSchoolObject(string $name, string $teacher) {
        $req = '{"id": "2", "jsonrpc": "2.0", "method": "addSchoolObject", "params": {"schoolObject": {"name": "'. $name . '", "teacher": "' . $teacher . '"} }}';
        $this->urlCall($req);
    }

    private function createStudents() {
        echo "Creating students ... </br>";
        $this->createStudent("alex", "244");
        $this->createStudent("iulia", "244");
    }

    private function createStudent(string $name, string $group) {
        $req = '{"id": "1", "jsonrpc": "2.0", "method": "addStudent", "params": {"student": {"name": "'. $name . '", "group": "' . $group . '"} }}';
        $this->urlCall($req);
    }

    private function getGradesForStudent(string $student) {
        echo "Getting grades for student: " . $student . "</br>";
        $req = '{"id": "5", "jsonrpc": "2.0", "method": "getCatalogForStudent", "params": {"student": "' . $student . '"}}';
        $resp = $this->urlCall($req);
        $this->showTableForGrades($resp["result"], ["Student", "SchoolObject", "Value"]);
    }

    private function getGradesForSchoolObject(string $schoolObject) {
        echo "Getting grades for schoolObject: " . $schoolObject . "</br>";
        $req = '{"id": "6", "jsonrpc": "2.0", "method": "getCatalogForSchoolObject", "params": {"schoolObject": "' . $schoolObject . '"}}';
        $resp = $this->urlCall($req);
        $this->showTableForGrades($resp["result"], ["Student", "SchoolObject", "Value"]);
    }

    private function updateStudents() {
        $this->updateStudent("alex", "22");
        $this->updateStudent("iulia", "21");
    }

    private function updateSchoolObjects() {
        $this->updateSchoolObject("mate", "voiculescu");
        $this->updateSchoolObject("info", "marinela");
        $this->updateSchoolObject("fizica", "gurzau");
        $this->updateSchoolObject("romana", "minodora");
    }

    private function updateGrades() {
        $this->updateGrade("alex", "mate");
        $this->updateGrade("alex", "info");
        $this->updateGrade("alex", "romana");
        $this->updateGrade("iulia", "mate");
        $this->updateGrade("iulia", "fizica");
        $this->updateGrade("iulia", "info");
    }

    private function deleteStudents() {
        $this->deleteStudent("iulia");
    }

    private function deleteSchoolObjects() {
        $this->deleteSchoolObject("romana");
        $this->deleteSchoolObject("fizica");
    }

    private function deleteSchoolObject(string $schoolObject) {
        $req = '{"id": "11", "jsonrpc": "2.0", "method": "deleteSchoolObject", "params": {"schoolObject": "' . $schoolObject . '"}}';
        $this->urlCall($req);
    }

    private function deleteStudent(string $student) {
        $req = '{"id": "10", "jsonrpc": "2.0", "method": "deleteStudent", "params": {"student": "' . $student . '"}}';
        $this->urlCall($req);
    }

    private function updateGrade(string $student, string $schoolObject) {
        $req = '{"id": "9", "jsonrpc": "2.0", "method": "updateGrade", "params": 
            {"grade": { 
                    "student": { "name": "' . $student . '" }, 
                    "schoolObject": { "name": "' . $schoolObject . '" },
                    "value": "' . rand(1, 10) . '"
                }
            }
        }';
        $this->urlCall($req);
    }

    private function updateSchoolObject(string $schoolObject, string $teacher) {
        $req = '{"id": "8", "jsonrpc": "2.0", "method": "updateSchoolObject", "params": {"schoolObject": {"name": "' . $schoolObject . '", "teacher" : "' .
            $teacher . '"}}}';
        $this->urlCall($req);
    }

    private function updateStudent(string $student, string $group) {
        $req = '{"id": "7", "jsonrpc": "2.0", "method": "updateStudent", "params": {"student": {"name": "' . $student . '", "group" : "' . $group . '"}}}';
        $this->urlCall($req);
    }

    private function showTable(array $objects, array $columns) {
        echo "<table style='border: 1px solid black;'>";
        echo "<tr style='border: 1px solid black;'>";
        foreach ($columns as $column) {
            echo "<td style='border: 1px solid black;background: aqua'>$column</td>";
        }
        echo "</tr>";
        foreach ($objects as $object) {
            echo "<tr style='border: 1px solid black;'>";
            for ($i = 0; $i < count($columns); $i++) {
                echo "<td style='border: 1px solid black;'>" . $object[$columns[$i]] . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }

    private function showTableForGrades(array $grades, array $columns) {
        echo "<table style='border: 1px solid black;'>";
        echo "<tr style='border: 1px solid black;'>";
        foreach ($columns as $column) {
            echo "<td style='border: 1px solid black;background: aqua'>$column</td>";
        }
        echo "</tr>";
        foreach ($grades as $grade) {
            echo "<tr style='border: 1px solid black;'>";
            echo "<td style='border: 1px solid black;'>" . $grade["student"]["name"] . "</td>";
            echo "<td style='border: 1px solid black;'>" . $grade["schoolObject"]["name"] . "</td>";
            echo "<td style='border: 1px solid black;'>" . $grade["value"] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

$client = new JsonRPCPHPClient("http://localhost/php/app/JsonRPCPHPServer.php");
$client->run();