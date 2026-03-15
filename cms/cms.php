<?php
class SCMS
{
    public $data;

    public const LONG = 'long';
    public const SHORT = 'short';
    private const DEFAULT_TEXTS = [
        self::LONG => "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis condimentum augue id magna semper rutrum. Maecenas lorem. Duis risus. Integer tempor. Aliquam id dolor. Praesent dapibus. Duis bibendum, lectus ut viverra rhoncus, dolor nunc faucibus libero, eget facilisis enim ipsum id lacus. Etiam commodo dui eget wisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Praesent id justo in neque elementum ultrices. Maecenas lorem. Morbi leo mi, nonummy eget tristique non, rhoncus non leo. In laoreet, magna id viverra tincidunt, sem odio bibendum justo, vel imperdiet sapien wisi sed libero.",
        self::SHORT => "Lorem ipsum"
    ];

    public function __construct()
    {
        $this->loadData();
    }

    public function loadData(){
        $json = file_get_contents(__DIR__ . "/db.json");
        $this->data = json_decode($json, true);
    }

    public function saveData(){
        return file_put_contents(__DIR__ . "/db.json", json_encode($this->data, JSON_PRETTY_PRINT), LOCK_EX);
    }

    public function throwError($fatal = false, $info = ""){
            die("Někde se stala chyba :("."<br>".$info);

        
    }


    public function setPassword($newPassword)
    {
        $this->data["config"]["password"] = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->saveData();
    }

    public function verifyPassword($password){
        return password_verify($password,$this->data["user"]["password"]);
    }

    public function text($id, $length = self::LONG){
        $result = true;
        if(!isset($this->data["blocks"][$id])){
             if(isset(self::DEFAULT_TEXTS[$length])){
                $this->data["blocks"][$id] = self::DEFAULT_TEXTS[$length];
            }else{
                $this->data["blocks"][$id] = self::DEFAULT_TEXTS["long"];
            }
            
            $result = $this->saveData();
        }

        if($result){
            echo "<span class='block'>".$this->data["blocks"][$id]."</span>";
        }else{
            $this->throwError(info: "Text se nepodařilo načíst.");
        }
        
    }


}
