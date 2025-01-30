<?php

namespace App\Service;

use App\Entity\CKGroup;
use App\Entity\Competence;
use App\Entity\Direction;
use App\Entity\Education;
use App\Entity\Faculty;
use App\Entity\Group;
use App\Entity\Program;
use App\Entity\Student;
use App\Entity\University;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ExcelProcessor
{
    private array $tableHead = [];
    private array $programs = [];
    private array $directions = [];
    private array $competencies = [];
    private array $students = [];
    private array $universities = [];
    private array $studentGroups = [];
    private array $faculties = [];
    private array $ckGroups = [];
    private string $dateFormat = 'd/m/Y';
    private ObjectManager $em;
    private int $batchSize = 500;

    public function __construct(private readonly ManagerRegistry $doctrine, private readonly FileHelper $fileHelper)
    {
        $this->em = $this->doctrine->getManager();
    }

    public function processEducationExcel(UploadedFile $file): array
    {
        set_time_limit('0');
        ini_set('max_execution_time', '600');
        ini_set('memory_limit', '1024M');
        if ($file->isValid()) {
            $filePath = $this->fileHelper->saveFileFromTmp($file);
            $reader = null;
            try {
                $reader = ReaderEntityFactory::createReaderFromFile($filePath);
                $reader->open($filePath);

                $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

                $this->prepareEducationData();

                foreach ($reader->getSheetIterator() as $sheet) {
                    $rowIndex = 1;
                    foreach ($sheet->getRowIterator() as $row) {
                        $rowData = $row->toArray();

                        if (1 == $rowIndex) {
                            // $this->tableHead = $rowData;
                            ++$rowIndex;
                            continue;
                        }

                        $this->processRow($rowData);
                        unset($rowData);

                        if (($rowIndex % $this->batchSize) === 0) {
                            $this->em->flush();

                            gc_collect_cycles();
                        }

                        ++$rowIndex;
                    }
                }

                $this->em->flush();
                $reader->close();
            } catch (\Exception $e) {
                $reader->close();
                unlink($filePath);

                return ['result' => 'error', 'message' => $e->getMessage()];
            }

            unlink($filePath);

            return ['result' => 'success'];
        } else {
            return ['result' => 'error', 'message' => 'Wrong file!'];
        }
    }

    private function processRow(array $rowData): void
    {
        // $em = $this->doctrine->getManager();
        /* пояснения к заголовку файла
          0 => "Наименование оценочной сессии" - program_name
          1 => "ID оценочной сессии" - education_inopolis_id
          2 => "Опубликование макета" - education_layout_date
          3 => "Отраслевая принадлежность" - direction_name
          4 => "Обучающиеся направления" - program_it
          5 => "Трудоемкость программы" - program_hours
          6 => "Поток" - education_flow
          7 => "Срок реализации программы" - program_realization_time
          8 => "Наименование компетенции" - competence_name
          9 => "Целевой уровень развития компетенции" - competence_level
          10 => "ID пользователя" - student_inopolis_id
          11 => "ФИО" - student_fio
          12 => "СНИЛС" - student_snils
          13 => "E-mail" - student_email
          14 => "Номер телефона" - student_phone
          15 => "Дата рождения" - student_birthday
          16 => "Дата регистрации" - student_registration_date
          17 => "Дата начала прохождения оценки" - education_date
          18 => "Статус" - education_status
          19 => "Итоговый уровень сформированности компетенций" - education_competence_level
          20 => "Итоговый уровень развития компетенции" - education_competence_grow_level
          21 => "Результат" - education_result
          22 => "Количество попыток" - education_attempts
          23 => "Время результирующей попытки" - education_attempt_time
          24 => "Состояние" - student_status
          25 => "Этап оценки" - education_stage
          26 => "Внутренний прокторинг" - education_internal_proctoring
          27 => "Внешний прокторинг" - education_external_proctoring
          28 => "Фото для прокторинга" - education_proctoring_photo
         */

        if (empty($rowData[11])) {
            return;
        }

        $education = new Education();

        if (empty($this->programs[$rowData[0]])) {
            $program = new Program();
            $program->setName($rowData[0]);
            $program->setIT('IT' == $rowData[4]);
            $program->setHours($rowData[5]);
            $program->setRealizationTime($rowData[7]);
            $this->em->persist($program);
            $this->programs[$program->getName()] = $program;
            $education->setProgram($program);
        } else {
            $education->setProgram($this->programs[$rowData[0]]);
        }

        if (empty($this->directions[$rowData[3]])) {
            $direction = new Direction();
            $direction->setName($rowData[3]);
            $this->em->persist($direction);
            $this->directions[$direction->getName()] = $direction;
            $education->setDirection($direction);
        } else {
            $education->setDirection($this->directions[$rowData[3]]);
        }

        if (empty($this->competencies[$rowData[8]])) {
            $competence = new Competence();
            $competence->setName($rowData[8]);
            $competence->setLevel($rowData[9]);
            $this->em->persist($competence);
            $this->competencies[$competence->getName()] = $competence;
            $education->setCompetence($competence);
        } else {
            $education->setCompetence($this->competencies[$rowData[8]]);
        }

        if (empty($this->students[$rowData[10]])) {
            $student = new Student();
            $student->setInopolisId($rowData[10]);
            $student->setFio($rowData[11]);
            $student->setEmail($rowData[13]);
            $student->setPhone($rowData[14]);
            $student->setSnils($rowData[12]);
            $student->setStatus($rowData[24]);
            if ('0/0/0' == $rowData[15]) {
                $student->setBirthday(null);
            } else {
                $student->setBirthday(\DateTime::createFromFormat($this->dateFormat, $rowData[15]));
            }
            $student->setRegistrationDate(\DateTime::createFromFormat($this->dateFormat, $rowData[16]));
            $this->em->persist($student);
            $this->students[$student->getInopolisId()] = $student->getId();
            $education->setStudent($student);
        } else {
            $ref = $this->em->getReference(Student::class, $this->students[$rowData[10]]);
            $education->setStudent($ref);
        }

        $education->setDate(\DateTime::createFromFormat($this->dateFormat, $rowData[17]) ?: null);
        $education->setFlow($rowData[6]);
        $education->setStatus($rowData[18]);
        $education->setLayoutDate(\DateTime::createFromFormat($this->dateFormat, $rowData[2]));
        $education->setInopolisId($rowData[1]);
        $education->setCompetenceLevel($rowData[19]);
        $education->setCompetenceGrowLevel($rowData[20]);
        $education->setResult($rowData[21]);
        $education->setAttempts(is_int($rowData[22]) ? $rowData[22] : null);
        $education->setResultAttemptTime($rowData[23]);
        $education->setStage($rowData[25]);
        $education->setInternalProctoring($rowData[26]);
        $education->setExternalProctoring($rowData[27]);
        $education->setProctoringPhoto($rowData[28]);

        $this->em->persist($education);
    }

    private function prepareEducationData(): void
    {
        $programs = $this->doctrine->getRepository(Program::class)->findAll();
        $directions = $this->doctrine->getRepository(Direction::class)->findAll();
        $competencies = $this->doctrine->getRepository(Competence::class)->findAll();
        $students = $this->doctrine->getRepository(Student::class)->findAll();

        foreach ($programs as $program) {
            $this->programs[$program->getName()] = $program;
        }

        foreach ($directions as $direction) {
            $this->directions[$direction->getName()] = $direction;
        }

        foreach ($competencies as $competence) {
            $this->competencies[$competence->getName()] = $competence;
        }

        foreach ($students as $student) {
            $this->students[$student->getInopolisId()] = $student->getId();
        }
    }

    public function processFacultyExcel(UploadedFile $file): array
    {
        if ($file->isValid()) {
            $filePath = $this->fileHelper->saveFileFromTmp($file);
            $reader = null;
            try {
                $reader = ReaderEntityFactory::createReaderFromFile($filePath);
                $reader->open($filePath);

                $this->prepareFacultyData();

                foreach ($reader->getSheetIterator() as $sheet) {
                    $rowIndex = 1;
                    foreach ($sheet->getRowIterator() as $row) {
                        $rowData = $row->toArray();

                        if (1 == $rowIndex) {
                            $this->fillTableHead($rowData);
                            ++$rowIndex;
                            continue;
                        }

                        $this->processFacultyRow($rowData);
                        unset($rowData);

                        if (($rowIndex % $this->batchSize) === 0) {
                            $this->em->flush();

                            gc_collect_cycles();
                        }

                        ++$rowIndex;
                    }
                }

                $reader->close();
            } catch (\Exception $e) {
                $reader->close();
                unlink($filePath);

                return ['result' => 'error', 'message' => $e->getMessage()];
            }

            unlink($filePath);

            return ['result' => 'success'];
        } else {
            return ['result' => 'error', 'message' => 'Wrong file!'];
        }
    }

    private function fillTableHead(array $rowData): void
    {
        foreach ($rowData as $index => $value) {
            if ('ФИО' == trim($value)) {
                $this->tableHead['fio'] = $index;
            }
            if ('ВУЗ' == trim($value)) {
                $this->tableHead['university'] = $index;
            }
            if ('Факультет' == trim($value)) {
                $this->tableHead['faculty'] = $index;
            }
            if ('Группа' == trim($value)) {
                $this->tableHead['student_group'] = $index;
            }
            if ('Группа ЦК' == trim($value)) {
                $this->tableHead['ck_group'] = $index;
            }
        }
    }

    private function prepareFacultyData(): void
    {
        $students = $this->doctrine->getRepository(Student::class)->findAll();
        $universities = $this->doctrine->getRepository(University::class)->findAll();
        $groups = $this->doctrine->getRepository(Group::class)->findAll();
        $faculties = $this->doctrine->getRepository(Faculty::class)->findAll();
        $ckGroups = $this->doctrine->getRepository(CKGroup::class)->findAll();

        foreach ($students as $student) {
            $this->students[$student->getFio()] = $student;
        }

        foreach ($universities as $university) {
            $this->universities[$university->getName()] = $university;
        }

        foreach ($groups as $group) {
            $this->studentGroups[$group->getName()] = $group;
        }

        foreach ($faculties as $faculty) {
            $this->faculties[$faculty->getName()] = $faculty;
        }

        foreach ($ckGroups as $ckGroup) {
            $this->ckGroups[$ckGroup->getName()] = $ckGroup;
        }
    }

    private function processFacultyRow(array $rowData): void
    {
        $student = null;

        foreach ($rowData as $index => $value) {
            if (empty($value)) {
                continue;
            }
            if ($index == $this->tableHead['fio']) {
                if (!empty($this->students[trim($value)])) {
                    /** @var $student Student */
                    $student = $this->students[trim($value)];
                } else {
                    continue;
                }
            }
            if (!$student) {
                continue;
            }
            if (!empty($this->tableHead['university']) and $index == $this->tableHead['university']) {
                if (empty($this->universities[trim($value)])) {
                    $university = new University();
                    $university->setName(trim($value));
                    $university->addStudent($student);
                    $this->em->persist($university);
                    $this->universities[$university->getName()] = $university;
                } else {
                    $student->setUniversity($this->universities[trim($value)]);
                    $this->em->persist($student);
                }
            }
            if (!empty($this->tableHead['faculty']) and $index == $this->tableHead['faculty']) {
                if (empty($this->faculties[trim($value)])) {
                    $faculty = new Faculty();
                    $faculty->setName(trim($value));
                    $faculty->addStudent($student);
                    $this->em->persist($faculty);
                    $this->faculties[$faculty->getName()] = $faculty;
                } else {
                    $student->setFaculty($this->faculties[trim($value)]);
                    $this->em->persist($student);
                }
            }
            if (!empty($this->tableHead['student_group']) and $index == $this->tableHead['student_group']) {
                if (empty($this->studentGroups[trim($value)])) {
                    $group = new Group();
                    $group->setName(trim($value));
                    $group->addStudent($student);
                    $this->em->persist($group);
                    $this->studentGroups[$group->getName()] = $group;
                } else {
                    $student->setStudentGroup($this->studentGroups[trim($value)]);
                    $this->em->persist($student);
                }
            }
            if (!empty($this->tableHead['ck_group']) and $index == $this->tableHead['ck_group']) {
                if (empty($this->ckGroups[trim($value)])) {
                    $ckGroup = new CKGroup();
                    $ckGroup->setName(trim($value));
                    $ckGroup->addStudent($student);
                    $this->em->persist($ckGroup);
                    $this->ckGroups[$ckGroup->getName()] = $ckGroup;
                } else {
                    $student->setCkGroup($this->ckGroups[trim($value)]);
                    $this->em->persist($student);
                }
            }
        }
    }
}
