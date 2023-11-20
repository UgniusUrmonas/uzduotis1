<!DOCTYPE html>
<html lang="en">

<body>
    <h1>Studio Ghibli Films</h1>

    <form method="get">
        <label for="title">Enter Movie Name:</label>
        <input type="text" id="title" name="title">
        <button type="submit">Search</button>

        <label for="running_time">Filter by Running Time:</label>
        <input type="text" id="running_time" name="running_time" placeholder="In minutes">
        <select id="comparison" name="comparison">
            <option value="=">=</option>
            <option value="<"><</option>
            <option value=">">></option>
            <option value="<="><=</option>
            <option value=">=">>=</option>
        </select>
        <button type="submit">Search</button>
    </form>

    <?php
    // Sukuriama klasę kurioje saugojama informaciją apie filmus
    class Movie
    {
        public $image;
        public $title;
        public $runningTime;
        public $description;

        // Inicijuoja filmo klasės ypatybes
        public function __construct($image, $title, $runningTime, $description)
        {
            $this->image = $image;
            $this->title = $title;
            $this->runningTime = $runningTime;
            $this->description = $description;
        }
    }

    class MovieList
    {
        public $movies = [];

        // Fetch'ina data iš Ghibli API
        public function fetch()
        {
            $api_url = 'https://ghibliapi.dev/films';
            $response = file_get_contents($api_url);
            $movieData = json_decode($response, true);

            foreach ($movieData as $data) {
                $movie = new Movie(
                    $data['image'],
                    $data['title'],
                    $data['running_time'],
                    $data['description']
                );

                $this->movies[] = $movie;
            }
        }
    }

    // Sukurkia nauja MovieList objektą
    $movieList = new MovieList();
    // Iškviečia gavimo metodą objekte $movieList
    $movieList->fetch();

    // Palygina filmo trukmę su nurodyta filtro verte
    function compareRunningTime($runningTime, $filterRunningTime, $compare)
    {
        switch ($compare) {
            case '>':
                return empty($filterRunningTime) || $runningTime > $filterRunningTime;
            case '<':
                return empty($filterRunningTime) || $runningTime < $filterRunningTime;
            case '>=':
                return empty($filterRunningTime) || $runningTime >= $filterRunningTime;
            case '<=':
                return empty($filterRunningTime) || $runningTime <= $filterRunningTime;
            default:
                return empty($filterRunningTime) || $runningTime == $filterRunningTime;
        }
    }

    // Patikrina ar URL užklausos eilutėje yra tam tikri parametrai
    $filterTitle = isset($_GET['title']) ? $_GET['title'] : '';
    $filterRunningTime = isset($_GET['running_time']) ? $_GET['running_time'] : '';
    $compare = isset($_GET['comparison']) ? $_GET['comparison'] : '=';

    echo '<ul>';
    // Nustato ar filmas atitinka filtravimo kriterijus
    foreach ($movieList->movies as $movie) {
        $titleMatch = empty($filterTitle) || stripos($movie->title, $filterTitle) !== false;
        $runningTimeMatch = empty($filterRunningTime) || compareRunningTime($movie->runningTime, $filterRunningTime, $compare);

        if ($titleMatch && $runningTimeMatch) {
            echo '<strong>Title:</strong> ' . $movie->title . '<br>';
            echo '<strong>Running Time: </strong> ' . $movie->runningTime . ' ' . 'min.' . '<br>';
            echo '<strong>Description:</strong> ' . $movie->description . '<br>';
            echo "<img src={$movie->image} width='500' height='400'>" . '<br>';
            echo '<br>';
        }
    }
    echo '</ul>';
    ?>
</body>

</html>
