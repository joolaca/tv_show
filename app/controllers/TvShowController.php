<?php
class TvShowController extends Controller
{
    public $Show;

    function __construct()
    {
        parent::__construct();
        $this->Show = new Show();

    }

    //set used model
    protected $my_models = [
        'Show'
    ];

    public function index(){
        $shows = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['search_filter'] = $_POST;
            $shows = $this->Show->getFiltered($_POST);
        }

        $search_block = $this->renderSearchBlock();

        return $this->render('tv_news_paper/list', compact('shows', 'search_block'));
    }

    /**
     * @return string
     */
    private function renderSearchBlock(){
        $dates = $this->Show->getDistictField('date');
        $channels = $this->Show->getDistictField('channel');
        return $this->render('tv_news_paper/search', compact('channels', 'dates'));
    }
}