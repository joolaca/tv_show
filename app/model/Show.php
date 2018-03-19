<?php

class Show extends Model
{
    private $channel;
    private $start;
    private $title;
    private $description;
    private $age_limit;
    private $date;

    protected $table = 'tv_show';


    public function getFiltered($filters){
        $statement = "SELECT ".implode(',',$this->getAllFildes()).
            " FROM ".$this->table;

        $statement .= $this->convertFilterToString($filters);

        return $this->query($statement);
    }

    /**
     * @param $filters = $_POST
     * @return string injection sql ' WHERE brand Like '%LG%'  '
     */
    private function convertFilterToString($filters){
        $filter_str = '';
        foreach ($filters as $key => $value) {

            if(in_array($key, $this->getAllFildes())
                && $value != ''
            ){
                //TODO need mysqli_escape_string
                $filter_str .= $key." LIKE '%".$value."%' AND ";
            }
        }
        $filter_str = substr($filter_str, 0, -4);

        if($filter_str != ''){
            $filter_str = ' WHERE '.$filter_str;
        }

        return $filter_str;
    }

    public function getAllFildes(){
        return [
            'channel',
            'start',
            'title',
            'description',
            'age_limit',
            'date',
        ];
    }


    /**
     * @param string $field
     * @return array DISTINCT(col)
     */
    public function getDistictField($field){
        $statement = "SELECT DISTINCT(".$field.") FROM ".$this->table;
        $result = $this->query($statement);
        foreach ($result as $item) {
            $out[] = $item[$field];
        }
        return $out;
    }


    /*---------------------------
    * GET SET
    *---------------------------*/



    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param mixed $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param mixed $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getAgeLimit()
    {
        return $this->age_limit;
    }

    /**
     * @param mixed $age_limit
     */
    public function setAgeLimit($age_limit)
    {
        $this->age_limit = $age_limit;
    }


}