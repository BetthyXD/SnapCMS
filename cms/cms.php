<?php
class SCMS
{
    private $data;
    private $alerts = [];

    public const LONG = 'long';
    public const SHORT = 'short';
    private const DEFAULT_TEXTS = [
        self::LONG => "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis condimentum augue id magna semper rutrum. Maecenas lorem. Duis risus. Integer tempor. Aliquam id dolor. Praesent dapibus. Duis bibendum, lectus ut viverra rhoncus, dolor nunc faucibus libero, eget facilisis enim ipsum id lacus. Etiam commodo dui eget wisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Praesent id justo in neque elementum ultrices. Maecenas lorem. Morbi leo mi, nonummy eget tristique non, rhoncus non leo. In laoreet, magna id viverra tincidunt, sem odio bibendum justo, vel imperdiet sapien wisi sed libero.",
        self::SHORT => "Lorem ipsum"
    ];

    public const ERROR = 'error';
    public const WARNING = 'warning';
    public const SUCCESS = 'success';
    private const ALERT_ICONS = [
        self::ERROR => "fa-circle-xmark",
        self::WARNING => "fa-triangle-exclamation",
        self::SUCCESS => "fa-circle-check"
    ];


    public function __construct()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $json = file_get_contents(__DIR__ . "/db.json");
        $this->data = json_decode($json, true);
    }

    public function saveData()
    {
        return file_put_contents(__DIR__ . "/db.json", json_encode($this->data, JSON_PRETTY_PRINT), LOCK_EX);
    }

    public function link()
    {
        $assets = '<link rel="stylesheet" href="' . $this->getBaseURL() . 'cms/style.css?' . time() . '">';
        $assets .= '<link rel="preconnect" href="https://fonts.googleapis.com">
                    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">';
        $assets .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">';

        if (isset($this->data["config"]["primaryColor"])) {
            $style = '<style>.snapCMS{--primary:' . $this->data["config"]["primaryColor"] . '}</style>';
        } else {
            $style = "";
        }

        return $assets . $style;
    }

    public function getBaseURL()
    {
        return $this->data["config"]["base"] ?? "/";
    }

    public function getUrls()
    {
        return [
            "loginURL" => $this->data["config"]["loginURL"] ?? "login",
            "logoutURL" => $this->data["config"]["logoutURL"] ?? "logout"
        ];
    }

    public function throwError($fatal = false, $info = "Někde se stala chyba :(")
    {
        $this->alert($info);
    }

    public function throw404()
    {
        http_response_code(404);
        if (file_exists(__DIR__ . "/../404.html")) {
            include __DIR__ . "/../404.html";
        } else {
            echo <<<HTML
                <!DOCTYPE html>
                <html lang="cs">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>SnapCMS - 404</title>
                    {$this->link()}
                </head>
                <body id="error404" class="snapCMS">
                    <div>
                        <h1 class="primary">404</h1>
                        <span>Stránka nebyla nalezena</span>
                    <div>
                </body>
                </html>
                HTML;
        }
        exit;
    }


    public function setPassword($newPassword)
    {
        $this->data["config"]["password"] = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->saveData();
    }

    public function verifyPassword($password)
    {
        return password_verify($password, $this->data["config"]["password"]);
    }

    public function login($password)
    {
        if ($this->verifyPassword($password)) {
            session_regenerate_id(true);
            $_SESSION["logged"] = true;
            $_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
            return true;
        } else {
            return false;
        }
    }

    public function logout()
    {
        $_SESSION = [];
        session_destroy();
        header("Location: " . $this->getBaseURL());
        exit;
    }

    public function isLoggedIn()
    {
        return (
            isset($_SESSION['logged']) &&
            $_SESSION['logged'] === true &&
            $_SESSION['userAgent'] === $_SERVER['HTTP_USER_AGENT']
        );
    }

    public function alert($text, $type = self::ERROR)
    {
        $this->alerts[] = [
            "text" => $text,
            "type" => $type
        ];
    }

    public function showAlerts()
    {
        $result = "";
        foreach ($this->alerts as $alert) {
            $icon = self::ALERT_ICONS[$alert['type']];
            $result .= <<<HTML
                        <div class="alert {$alert['type']} " onclick='this.remove()'>
                            <i class="fa-solid $icon"></i>{$alert['text']}
                        </div>
                        HTML;
        }
        echo '<div class="snapCMS"><div class="alertBox">' . $result . '</div></div>';
    }

    public function showAdminBar()
    {
        if ($this->isLoggedIn()) {
            $logoutURL = $this->getBaseURL().$this->getURLs()['logoutURL'];
            echo <<<HTML
            <div class="snapCMS">
                <div id="adminBar">
                    <a href="{$logoutURL}">Odhlásit se</a>
                    <form action="" method="post" onsubmit="loadChanges()">
                        <input name="changes" id="changes" type="hidden">
                        <button type="submit">Uložit</button>
                    </form>
                </div>
            </div>
   <script>
    function loadChanges() {
        const editables = document.querySelectorAll('.editable');
        const dataToSave = {};

        editables.forEach(el => {
            const key = el.getAttribute('data-key');
            const value = el.innerText;
            
            if (key) {
                dataToSave[key] = value;
            }
        });
        document.getElementById('changes').value = JSON.stringify(dataToSave);
    }

    document.addEventListener('DOMContentLoaded', () => {
    
    const labelsToSwap = document.querySelectorAll('label:has(.editable)');
    labelsToSwap.forEach(label => {
        const span = document.createElement('span');
        
        Array.from(label.attributes).forEach(attr => {
            span.setAttribute(attr.name, attr.value);
        });
        
        span.innerHTML = label.innerHTML;
        label.parentNode.replaceChild(span, label);
    });

    const buttonsToFix = document.querySelectorAll('button:has(.editable)');
    
    buttonsToFix.forEach(btn => {
        btn.setAttribute('type', 'button');
        
        btn.addEventListener('click', (e) => {
            if (e.target.closest('.editable')) {
                e.stopPropagation();
            }
        });
    });

});

        </script>
HTML;
        }
    }

    public function updateData($blocks){
        if($this->isLoggedIn()){
            $newData = json_decode($blocks, true);

            foreach($newData as $key => $value){
                $this->data["blocks"][$key] = strip_tags($value);
            }
            return $this->saveData();
        }
        
    }

    public function text($id, $length = self::LONG)
    {
        $editable = $this->isLoggedIn();
        $result = true;
        if (!isset($this->data["blocks"][$id])) {
            if (isset(self::DEFAULT_TEXTS[$length])) {
                $this->data["blocks"][$id] = self::DEFAULT_TEXTS[$length];
            } else {
                $this->data["blocks"][$id] = self::DEFAULT_TEXTS["long"];
            }

            $result = $this->saveData();
        }

        if ($result) {
            echo "<span class='block " . ($editable ? "editable" : "") . "'" . ($editable ? "contenteditable='true' data-key='" . $id . "'" : "") . ">" . htmlspecialchars($this->data["blocks"][$id]) . "</span>";
        } else {
            $this->throwError(info: "Text se nepodařilo načíst.");
        }
    }
}

session_start();
$cms = new SCMS();

if(isset($_POST["changes"])){
    if($cms->updateData($_POST["changes"])){
        $cms->alert("Stránka byla uložena.", SCMS::SUCCESS);
    }else{
        $cms->alert("Stránku se nepodařilo uložit.", SCMS::ERROR);
    }
}