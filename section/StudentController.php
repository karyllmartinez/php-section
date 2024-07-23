<?php

$requestID = null;
if (isset($uri[2])) {
    $requestID = (int) $uri[2];
}
$bearer_token = '';
if (!empty($_SERVER['HTTP_AUTHORIZATION'])){
    $auth_header = $_SERVER['HTTP_AUTHORIZATION'];
    if(strpos($auth_header,'Bearer')=== 0){
        $bearer_token = substr($auth_header,7);
    }
}
$file = $getfile;
$requestMethod = $_SERVER["REQUEST_METHOD"];
$controller = new FloatingmoduleController($requestMethod, $requestID,$bearer_token,$file);
$controller->processRequest();

class FloatingmoduleController
{
    private $requestMethod;
    private $requestID;
    private $bearer_token;
    private $file;
    public function __construct($requestMethod, $requestID,$bearer_token,$file)
    {  
        $this->requestMethod = $requestMethod;
        $this->requestID = $requestID;
        $this->bearer_token = $bearer_token;
        $this->file = $file;
    }
    public function processRequest()
    {
        switch ($this->requestMethod) {
            
                case 'PATCH':
                    $response = $this->patch();
                    ;
                break;
                case 'DELETE':
                    $response = $this->delete();
                    ;
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    
    private function patch(){
        require "bootstrap.php"; 
        require "src\section\Section.php";
    
        $newUser = new User();
        $token = new Tokens();
    
        $result = $token->getValidation($this->bearer_token);
        $input = (array) json_decode(file_get_contents('php://input'), true);
    
        if($result == true){
              $origin = $entityManager->find(Section::class, $input["origin_id"]);
              $destination = $entityManager->find(Section::class, $input["destination_id"]);
              $student = $entityManager->find(Student::class, $input["student_id"]);
              $origin->removeStudent($origin->getStudent(),$student);
              $entityManager->flush();
              $destination->setStudent($student);
              $entityManager->flush();

                    $sectionRepository = $entityManager->getRepository('Section')->findAll();
                    $sectionList = [];
                    foreach ($sectionRepository as $section) {
                        $studentList = [];
                        foreach ($section->getStudent() as $student) {
                            array_push($studentList, [
                                "id" => $student->id(), 
                                "fname" => $student->fname(), 
                                "lname" => $student->lname()
                            ]);
                        } 
                        array_push($sectionList, [
                            "id" => $section->id(), 
                            "description" => $section->description(),
                            "type" => $section->Type()->description(),
                            "student" => $studentList
                        ]);
                    }
    
                    $response['status_code_header'] = 'HTTP/1.1 200 OK';
                    $response['body'] = json_encode($sectionList);
                    return $response;
    
     
        } else {
            $response['status_code_header'] = 'HTTP/1.1 401 Unauthorized';
            $response['body'] = json_encode(["Message" => "Invalid Token"]);
            return $response;
        }
    }

    private function delete(){
        require "bootstrap.php"; 
        require "src\section\section.php" ;
        $newUser = new User();
        $token = new Tokens();

        $result = $token->getValidation($this->bearer_token);
        $input = (array) json_decode(file_get_contents('php://input'), true);

       
        if($result == true){

            //get specific id to update the table section
            $section = $entityManager->find(Section::class,$input["section_id"]);
            $section -> clearStudent(); 
            $entityManager->flush();

            $sectionRepository = $entityManager->getRepository('section')->findAll();
            $sectionList = [];
            foreach ($sectionRepository as $section) {
                $studentList = [];
                foreach ($section -> getStudent() as $student) {
                    array_push ($studentList, [
                    "id" => $student -> id(), 
                    "fname" => $student -> fname(), 
                    "lname" => $student -> lname() ]);

                } 
                array_push ($sectionList, ["id" => $section -> id(), 
                "description" => $section -> description(),
                "student" => $studentList]);
            }

            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode( $sectionList);
            return $response;

        }else{
            $response['status_code_header'] = 'HTTP/1.1 401 Unauthorized';
            $response['body'] = json_encode(["Message" => "Invalid Token"]);
            return $response;
        }
    }



    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = json_encode(["Message" => "Method not allowed"]);
        return $response;
    }




}


