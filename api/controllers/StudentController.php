<?php
require_once "services/StudentServices.php";
require_once "repositories/StudentRepository.php";
require_once "config/Database.php"; // Ensure Database class is included

class StudentController {
    private StudentService $service;
    private StudentRepository $repository;
    private $dbConnection;

    public function __construct() {
        $db = new Database();
        $this->dbConnection = $db->getConnection();
        $this->repository = new StudentRepository($this->dbConnection, "Student");
        $this->service = new StudentService($this->repository);
    }

    public function GetAllStudents(): void {
        echo json_encode($this->repository->GetAllList());
    }

    public function GetStudentById(int $studentId): void {
        echo json_encode($this->repository->GetById($studentId));
    }

    public function AddStudent($data): void {
        $this->service->addStudent($data);
        echo "Data Added Successfully"; 
    }

    public function UpdateStudent($data): void {
        $studentId = $data['id'];
        $this->service->updateStudent($data, $studentId);
        echo "Data Updated Successfully";
    }

    public function DeleteStudent(int $studentId): void {
        $this->repository->Delete($studentId);
        echo "Data Deleted Successfully";
    }
}