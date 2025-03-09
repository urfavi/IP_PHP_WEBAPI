<?php
require_once "config/Database.php";
require_once "models/Student.php";
require_once "contracts/IBaseRepository.php";

class StudentRepository implements IBaseRepository {
    protected $dbContext;
    protected $tableName;
    
    public function __construct($dbContext, $tableName) {
        $this->dbContext = $dbContext;
        $this->tableName = $tableName;
    }

    public function GetAllList() : array {
        $query = "SELECT * FROM {$this->tableName}";
        $result = $this->ExecuteSqlQuery($query, []);

        return $this->BuildResultList($result);
    }

    public function GetById(int $id) : ?object {
        $query = "SELECT * FROM {$this->tableName} WHERE id = :id";
        $result = $this->ExecuteSqlQuery($query, [':id' => $id]);

        return $this->BuildResult($result);
    }

    public function Add($student): Student {
        if (!isset($student->name) || empty(trim($student->name)) || !isset($student->midterm_score) || !isset($student->final_score)) {
            throw new Exception("Student name and scores are required");
        }
    
        $query = "INSERT INTO {$this->tableName} (name, midterm_score, final_score, final_grade, status) 
                  VALUES (:name, :midterm_score, :final_score, :final_grade, :status)";

        $params = [
            ':name' => $student->name,
            ':midterm_score' => $student->midterm_score,
            ':final_score' => $student->final_score,
            ':final_grade' => $student->final_grade,
            ':status' => $student->status
        ];

        $this->ExecuteSqlQuery($query, $params);
        return $student;
    }

    public function Update($student) : void {
        if (empty($student->midterm_score) || empty($student->final_score)) {
            throw new Exception("Student scores are required");
            return;
        }
        
        $query = "UPDATE {$this->tableName} 
                  SET midterm_score = :midterm_score, final_score = :final_score, final_grade = :final_grade, status = :status
                  WHERE id = :id";

        $params = [
            ':midterm_score' => $student->midterm_score,
            ':final_score' => $student->final_score,
            ':final_grade' => $student->final_grade,
            ':status' => $student->status,
            ':id' => $student->id
        ];

        $this->ExecuteSqlQuery($query, $params);
    }

    public function Delete(int $id) : void {
        $query = "DELETE FROM {$this->tableName} WHERE ID = :id";
        $params = [':id' => $id];
        $this->ExecuteSqlQuery($query, $params);
    }

    private function ExecuteSqlQuery(string $query, array $params) {
        $statementObject = $this->dbContext->prepare($query);
        $statementObject->execute($params);

        if (stripos($query, "SELECT") === 0) {
            return $statementObject->fetchAll(PDO::FETCH_ASSOC);
        }

        return null;
    }

    private function BuildResult(?array $result) : ?Student {
        if (!$result || empty($result[0])) {
            return null;
        }

        $row = $result[0];

        $student = new Student();
        $student->id = $row['id'];
        $student->name = $row['name'];
        $student->midterm_score = (int) $row['midterm_score'];
        $student->final_score = (float) $row['final_score'];
        $student->final_grade = $row['final_grade'];
        $student->status = $row['status'];

        return $student;
    }

    private function BuildResultList(array $result) : array {
        $students = [];
        
        foreach ($result as $row) {
            $student = [
                'id' => ($row['id']),
                'name' => $row['name'],
                'midterm_score' => (int) $row['midterm_score'],
                'final_score' => (int) $row['final_score'],
                'final_grade' => (int) $row['final_grade'],
                'status' => $row['status'],
            ];
            $students[] = $student;
        }

        return $students;
    }
}