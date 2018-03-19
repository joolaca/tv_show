<?php
define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);
define('APP', ROOT . 'app'. DIRECTORY_SEPARATOR );

require APP . '/config/config.php';
require APP . '/core/Db.php';
require APP . '/core/Model.php';
require APP . '/model/Show.php';

class Install{

    private $conn;
    private $table = 'tv_show';
    private $date = '2018-03-10';

    function __construct()
    {
        return $this->run();
    }


    private function run(){

        $db = Db::getInstance();
        $this->conn = $db->getConnection();

        $this->migration();

        $tv_api = new TvApi($this->date);
        $tv_news_paper = $tv_api->getTvNewsPaper();
        $this->seed($tv_news_paper);

        echo("\n---end install---\n\n");
    }




    // create DB , Table
    private function migration(){

        try {

            $sql = "CREATE DATABASE IF NOT EXISTS ".DB_NAME. ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;';
            $this->conn->exec($sql);

            $sql = "use ".DB_NAME;
            $this->conn->exec($sql);
            $sql = "CREATE TABLE IF NOT EXISTS $this->table (
                id int(11) AUTO_INCREMENT PRIMARY KEY,
                channel varchar(512),
                start time,
                title varchar(512),
                description text,
                age_limit varchar(512),
                date date

                )";
            $this->conn->exec($sql);
            echo "\nDB created successfully\n";
        }
        catch(PDOException $e)
        {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param array $tv_news_paper [Show Model]
     * @throws Exception
     */
    private function seed($tv_news_paper){

        try{
            $statement = $this->conn->prepare("INSERT INTO $this->table(
            channel, start, title, description, age_limit , date
          )
            VALUES(:channel, :start, :title, :description, :age_limit, :date)
          ");

            foreach ($tv_news_paper as $show) {

                $statement->execute([
                    "channel" => $show->getChannel(),
                    "start" => $show->getStart(),
                    "title" => $show->getTitle(),
                    "description" => $show->getDescription(),
                    "age_limit" => $show->getAgeLimit(),
                    "date" => $show->getDate(),
                ]);
            }
            echo "\n Insert Test Data \n";
        }catch(PDOException $e)
        {
            throw new Exception($e->getMessage());
        }

    }

}

class TvApi{

    public $date;
    private $tv_newspaper;
    function __construct($date = null)
    {
        if(is_null($date)){
               $this->date = date('Y-m-d');
        }else{
            $this->date = date('Y-m-d', strtotime($date));
        }
    }

    /*
     * curl port.hu api
     * @param string $date  2018-03-07
     * return stdClass
     */
    private function callTvApi(){
        $url = "https://port.hu/tvapi?channel_id%5B%5D=tvchannel-5&channel_id%5B%5D=tvchannel-3&channel_id%5B%5D=tvchannel-21&channel_id%5B%5D=tvchannel-325&channel_id%5B%5D=tvchannel-6&channel_id%5B%5D=tvchannel-103&date=".$this->date;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 4);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_json = curl_exec($ch);
        curl_close($ch);

        if(empty($response_json)){
            throw new Exception("Empty API response");
        }
        return json_decode($response_json);
    }

    /**
     * @param stdClass $result_api
     * @throws Exception
     */
    private function resultApiValidator($result_api){

        $error = "\n\n this old code , pls change convertApiResultToShowModel() function\n\n";

        if(!isset($result_api->channels)){
            throw new Exception($error);
        }

        if(!isset($result_api->channels[0]->programs)){
            throw new Exception($error);
        }
        if(!isset($result_api->channels[0]->name)){
            throw new Exception($error);
        }

        if(!isset($result_api->channels[0]->programs[0]->title)){
            throw new Exception($error);
        }

        if(!isset($result_api->channels[0]->programs[0]->restriction)){
            throw new Exception($error);
        }

        if(!isset($result_api->channels[0]->programs[0]->restriction->age_limit)){
            throw new Exception($error);
        }
    }

    private function convertApiResultToShowModel($result_api){
        foreach ( $result_api->channels as $channel) {
            $channel_name = $channel->name;

            foreach ($channel->programs as $program) {
                $show = new Show();
                $show->setChannel($channel_name);
                $show->setStart($program->start_time);
                $show->setTitle($program->title);
                $show->setDescription($program->description);
                $show->setAgeLimit($program->restriction->age_limit);
                $show->setDate($this->date);
                $this->tv_newspaper[] = $show;
            }
        }
        return $this->tv_newspaper;
    }

    /**
     * get tv shows to port.hu
     * @return mixed array [ Show Model ]
     * @throws Exception
     */
    public function getTvNewsPaper(){

        $result_api = $this->callTvApi();
        $this->resultApiValidator($result_api);
        $this->convertApiResultToShowModel($result_api);

        return $this->tv_newspaper;
    }
}

new Install();

?>