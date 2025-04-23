<?php
abstract class Importer {
    protected $pdo;
    protected $filename;

    public function __construct($pdo, $filename) {
        $this->pdo = $pdo;
        $this->filename = $filename;
    }

    abstract public function import(): void;
}

abstract class SemesterImporter extends Importer {
    abstract public function importWithSemester(string $semester, int $year): void;
}
