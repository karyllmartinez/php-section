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
            case 'GET':
                $response = $this->get();
                ;
                break;
                case 'POST':
                    $response = $this->post();
                    ;
                break;
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

    private function get(){
        require "bootstrap.php"; 
        require "src\section\section.php" ;
        $newUser = new User();
        $token = new Tokens();

        $result = $token->getValidation($this->bearer_token);

       
        if($result == true){
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
            $response['body'] = json_encode( $sectionList);
            return $response;

        }else{
            $response['status_code_header'] = 'HTTP/1.1 401 Unauthorized';
            $response['body'] = json_encode(["Message" => "Invalid Token"]);
            return $response;
        }

        
    }

    
    private function post(){
        require "bootstrap.php"; 
        require "src\section\section.php" ;
        $newUser = new User();
        $token = new Tokens();

        $result = $token->getValidation($this->bearer_token);
        $input = (array) json_decode(file_get_contents('php://input'), true);

       
        if($result == true){

            $newsection = new Section();
            $newsection -> setDescription($input["description"]);
            //input
            $entityManager->persist($newsection);
            //save
            $entityManager->flush();

            $sectionRepository = $entityManager->getRepository('section')->findAll();
            $sectionList = [];
            foreach ($sectionRepository as $section) {
                array_push ($sectionList, ["id" => $section -> id(), "description" => $section -> description() ]);

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

    private function patch(){
        require "bootstrap.php"; 
        require "src\section\section.php" ;
        $newUser = new User();
        $token = new Tokens();

        $result = $token->getValidation($this->bearer_token);
        $input = (array) json_decode(file_get_contents('php://input'), true);

       
        if($result == true){

            //get specific id to update the table section
            $newsection = $entityManager->find(Section::class,$this->requestID); 
            $newsection -> setDescription($input["description"]);
            
            $entityManager->flush();

            $sectionRepository = $entityManager->getRepository('section')->findAll();
            $sectionList = [];
            foreach ($sectionRepository as $section) {
                array_push ($sectionList, ["id" => $section -> id(), "description" => $section -> description() ]);

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

    private function delete(){
        require "bootstrap.php"; 
        require "src\section\section.php" ;
        $newUser = new User();
        $token = new Tokens();

        $result = $token->getValidation($this->bearer_token);
        $input = (array) json_decode(file_get_contents('php://input'), true);

       
        if($result == true){
            
            //get specific id to update the table section
            $newsection = $entityManager->find(Section::class,$this->requestID); 
            
            $entityManager -> remove($newsection);
            
            $entityManager->flush();

            $sectionRepository = $entityManager->getRepository('section')->findAll();
            $sectionList = [];
            foreach ($sectionRepository as $section) {
                array_push ($sectionList, ["id" => $section -> id(), "description" => $section -> description() ]);

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


