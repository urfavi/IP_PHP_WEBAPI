<?php
require_once "repositories/StudentRepository.php";

class StudentService {
    private StudentRepository $repository;

    public function __construct(StudentRepository $repository) {
        $this->repository = $repository;
    }

    public function calculateGrade($midtermScore, $finalScore) {
        return round(($midtermScore * 0.4) + ($finalScore * 0.6));
    }

    public function calculateStatus($finalGrade) {
        return $finalGrade >= 75 ? 'Pass' : 'Fail';
    }

    public function addStudent($data): Student {
        $finalGrade = $this->calculateGrade($data['midterm_score'], $data['final_score']);
        $status = $this->calculateStatus($finalGrade);

        $student = new Student();
        $student->name = $data['name'];
        $student->midterm_score = $data['midterm_score'];
        $student->final_score = $data['final_score'];
        $student->final_grade = $finalGrade;
        $student->status = $status;

        return $this->repository->Add($student);
    }

    public function updateStudent($data, $studentId): void {
        $existingStudent = $this->repository->GetById($studentId);
        $finalGrade = $this->calculateGrade($data['midterm_score'], $data['final_score']);
        $status = $this->calculateStatus($finalGrade);
    
        $student = new Student();
        $student->id = $studentId;
        $student->name = $existingStudent->name;
        $student->midterm_score = $data['midterm_score'];
        $student->final_score = $data['final_score'];
        $student->final_grade = $finalGrade;
        $student->status = $status;
    
        $this->repository->Update($student);
    }
}