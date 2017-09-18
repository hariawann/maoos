<?php
namespace Application\Models;
use Framework\Engine\Gear\Model;
use Framework\Tools\Benchmark\Benchmark;
use Framework\Tools\Api\RestAPI as api;
use Application\config\ConfigApp as c;
/**
 * class to describe s user into system in application
 */
class Users extends Model{
    public $id;
    public $name;
    public $email;
    public $phone;
    public $leveluser;
    public $description;
    public $createdat;
    public $updatedat;

    public $password;
    
    private function getId(){
        return $this->id;
    }
    private function setName(string $newName){
        $this->$name = $newName;
    }

    public function getName():string{
        return $this->name;
    }
    
    private function setEmail(string $new){
        $this->email = $new;
    }

    public function getEmail():string{
        return $this->email;
    }

    private function setPhone( $new){
        $this->phone= $new;
    }

    public function getPhone():integer{
        return $this->phone;
    }

    private function setLevelid( $new){
        $this->levelid = $new;
    }

    public function getLevelid():integer{
        return $this->levelid;
    }
    private function setDescription(string $new){
        $this->description = $new;
    }

    public function getDescription():string{
        return $this->description;
    }
    private function setCreatedat(string $new){
        $this->createdat = $new;
    }

    public function getCreatedat():string{
        return $this->createdat;
    }
    
    private function setUpdatedat(string $new){
        $this->updatedat = $new;
    }

    public function getUpdatedat():string{
        return $this->updatedat;
    }

    private function setPassword(string $new){
        $this->password = $new;
    }

    public function getPassword():string{
        return $this->password;
    }

}