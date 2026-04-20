<?php
class Seeder
{
    public static function seed(PDO $db): void
    {
        $topicCount = (int)$db->query('SELECT COUNT(*) FROM topics')->fetchColumn();
        if ($topicCount > 0) {
            return;
        }

        $userModel = new User($db);
        if (!$userModel->findByUsername('admin')) {
            $userModel->register('admin', 'admin@example.com', 'admin123', 'admin');
        }

        $topics = self::getTopicSeeds();
        $topicModel = new Topic($db);
        $questionModel = new Question($db);

        foreach ($topics as $topicName => $questions) {
            $topicId = $topicModel->addTopic($topicName, 'Temats: ' . $topicName);
            foreach ($questions as $questionData) {
                $questionModel->addQuestionWithAnswers($topicId, $questionData['question'], $questionData['answers']);
            }
        }
    }

    private static function getTopicSeeds(): array
    {
        return [
            'Sports' => [
                ['question' => 'Kurš sporta veids izmanto grozu un bumbu?', 'answers' => [['text' => 'Basketbols', 'correct' => true], ['text' => 'Futbols', 'correct' => false], ['text' => 'Hokejs', 'correct' => false], ['text' => 'Peldēšana', 'correct' => false]]],
                ['question' => 'Cik spēlētāji ir uz laukuma basketbolā vienā komandā?', 'answers' => [['text' => '5', 'correct' => true], ['text' => '11', 'correct' => false], ['text' => '7', 'correct' => false], ['text' => '6', 'correct' => false]]],
                ['question' => 'Kurš sporta veids izmanto bumbu un nūju uz aisberga?', 'answers' => [['text' => 'Hokejs', 'correct' => true], ['text' => 'Ritma gimnastika', 'correct' => false], ['text' => 'Basketbols', 'correct' => false], ['text' => 'Beisbols', 'correct' => false]]],
                ['question' => 'Cik caurumus ir tipiskā golfa laukumā?', 'answers' => [['text' => '18', 'correct' => true], ['text' => '9', 'correct' => false], ['text' => '12', 'correct' => false], ['text' => '24', 'correct' => false]]],
                ['question' => 'Kurš sporta veids izmanto tīklu un tenisa raketi?', 'answers' => [['text' => 'Teniss', 'correct' => true], ['text' => 'Volejbols', 'correct' => false], ['text' => 'Badmintons', 'correct' => false], ['text' => 'Galda teniss', 'correct' => false]]],
                ['question' => 'Kuru sporta veidu raksturo šaušanas un mērķa trāpīšana?', 'answers' => [['text' => 'Biatlons', 'correct' => true], ['text' => 'Regbijs', 'correct' => false], ['text' => 'Džudo', 'correct' => false], ['text' => 'Basketbols', 'correct' => false]]],
                ['question' => 'Kurā sporta veidā izmanto loku un bultu?', 'answers' => [['text' => 'Šaušana ar loku', 'correct' => true], ['text' => 'Biatlons', 'correct' => false], ['text' => 'Piedzīvojumu sacīkstes', 'correct' => false], ['text' => 'Hokejs', 'correct' => false]]],
                ['question' => 'Kuras spēles laukums ir 100 metrus garš un tajā ir drukāšanas mērķis?', 'answers' => [['text' => 'Amerikāņu futbols', 'correct' => true], ['text' => 'Futbols', 'correct' => false], ['text' => 'Rugby', 'correct' => false], ['text' => 'Lacrosse', 'correct' => false]]],
                ['question' => 'Cik setus parasti spēlē sieviešu Grand Slam mačā teniss?', 'answers' => [['text' => '3 setus', 'correct' => true], ['text' => '5 setus', 'correct' => false], ['text' => '2 setus', 'correct' => false], ['text' => '4 setus', 'correct' => false]]],
                ['question' => 'Kura sporta disciplīna izmanto stieni un arena riteni?', 'answers' => [['text' => 'Varbūt roko?', 'correct' => false], ['text' => 'Lecšana ar stieni', 'correct' => true], ['text' => 'Smaiļošana', 'correct' => false], ['text' => 'Kērlinga', 'correct' => false]]],
                ['question' => 'Kurā sportā tiek piemērota žoga sistēma un ostas pārvaldība?', 'answers' => [['text' => 'Nav šādas', 'correct' => true], ['text' => 'Peldēšana', 'correct' => false], ['text' => 'Beisbols', 'correct' => false], ['text' => 'Hokejs', 'correct' => false]]],
                ['question' => 'Kurš no šiem ir olimpiskais cīņas veids?', 'answers' => [['text' => 'Džudo', 'correct' => true], ['text' => 'Rodeo', 'correct' => false], ['text' => 'Krikets', 'correct' => false], ['text' => 'Lacrosse', 'correct' => false]]],
                ['question' => 'Kura sporta disciplīna ietver lēcienu pāri šķērslim?', 'answers' => [['text' => 'Ārts', 'correct' => false], ['text' => 'Hurdles', 'correct' => true], ['text' => 'Rugby', 'correct' => false], ['text' => 'Tennis', 'correct' => false]]],
                ['question' => 'Kurš sporta veids ir pazīstams ar "home run"?', 'answers' => [['text' => 'Beisbols', 'correct' => true], ['text' => 'Futbols', 'correct' => false], ['text' => 'Basketbols', 'correct' => false], ['text' => 'Hokejs', 'correct' => false]]],
                ['question' => 'Kura spēle tiek spēlēta ar nūju un grozu uz ledus?', 'answers' => [['text' => 'Hokejs', 'correct' => true], ['text' => 'Basketbols', 'correct' => false], ['text' => 'Futbols', 'correct' => false], ['text' => 'Beisbols', 'correct' => false]]],
            ],
            'Music' => [
                ['question' => 'Cik notis ir parastā oktāvā?', 'answers' => [['text' => '8', 'correct' => true], ['text' => '7', 'correct' => false], ['text' => '6', 'correct' => false], ['text' => '9', 'correct' => false]]],
                ['question' => 'Kurš instruments ir taustiņu instruments?', 'answers' => [['text' => 'Pianīns', 'correct' => true], ['text' => 'Vijole', 'correct' => false], ['text' => 'Saksofons', 'correct' => false], ['text' => 'Trapanis', 'correct' => false]]],
                ['question' => 'Kas ir kompozīcijas autors?', 'answers' => [['text' => 'Mūzikas radītājs', 'correct' => true], ['text' => 'Dziedātājs', 'correct' => false], ['text' => 'Producent', 'correct' => false], ['text' => 'Mākslinieks', 'correct' => false]]],
                ['question' => 'Kura gaisma mēra laiku mūzikā?', 'answers' => [['text' => 'Ritms', 'correct' => true], ['text' => 'Tonis', 'correct' => false], ['text' => 'Akkords', 'correct' => false], ['text' => 'Skaņa', 'correct' => false]]],
                ['question' => 'Kura mūzikas žanra pamatā ir repošana?', 'answers' => [['text' => 'Hip-hops', 'correct' => true], ['text' => 'Klasicisms', 'correct' => false], ['text' => 'Folk', 'correct' => false], ['text' => 'Džezs', 'correct' => false]]],
                ['question' => 'Kurš no šiem ir stīgu instruments?', 'answers' => [['text' => 'Vijole', 'correct' => true], ['text' => 'Flauta', 'correct' => false], ['text' => 'Čells', 'correct' => false], ['text' => 'Klavieres', 'correct' => false]]],
                ['question' => 'Kurš komponists ir radījis "Für Elise"?', 'answers' => [['text' => 'Bēthovens', 'correct' => true], ['text' => 'Mocarts', 'correct' => false], ['text' => 'Šopens', 'correct' => false], ['text' => 'Bahs', 'correct' => false]]],
                ['question' => 'Kurš instruments pieder pūšamo instrumentu grupai?', 'answers' => [['text' => 'Flauta', 'correct' => true], ['text' => 'Ģitāra', 'correct' => false], ['text' => 'Ņujork', 'correct' => false], ['text' => 'Bungu komplekts', 'correct' => false]]],
                ['question' => 'Kura daļa mūzikā nosaka melodijas augstumu?', 'answers' => [['text' => 'Tonālums', 'correct' => true], ['text' => 'Ritms', 'correct' => false], ['text' => 'Harmonija', 'correct' => false], ['text' => 'Teksts', 'correct' => false]]],
                ['question' => 'Kura daļa mūzikā ir akords?', 'answers' => [['text' => 'Vairāki noti vienlaikus', 'correct' => true], ['text' => 'Viena nota', 'correct' => false], ['text' => 'Pauze', 'correct' => false], ['text' => 'Dziesmas ritms', 'correct' => false]]],
                ['question' => 'Kurš no šiem ir popmūzikas instrumentāls ansamblis?', 'answers' => [['text' => 'Band', 'correct' => true], ['text' => 'Orķestris', 'correct' => false], ['text' => 'Koruss', 'correct' => false], ['text' => 'Solo', 'correct' => false]]],
                ['question' => 'Kurš instruments ir atslēgas instruments?', 'answers' => [['text' => 'Pianīns', 'correct' => true], ['text' => 'Saksofons', 'correct' => false], ['text' => 'Trapanis', 'correct' => false], ['text' => 'Flauta', 'correct' => false]]],
                ['question' => 'Kas ir albums?', 'answers' => [['text' => 'Dziesmu kopums', 'correct' => true], ['text' => 'Filma', 'correct' => false], ['text' => 'Koncerts', 'correct' => false], ['text' => 'Instrumentāls', 'correct' => false]]],
                ['question' => 'Kurš no šiem ir klasiskās mūzikas žanrs?', 'answers' => [['text' => 'Simfonija', 'correct' => true], ['text' => 'Rap', 'correct' => false], ['text' => 'Pop', 'correct' => false], ['text' => 'Rock', 'correct' => false]]],
                ['question' => 'Kura no šīm ir mūzikas dinamika?', 'answers' => [['text' => 'Loudness', 'correct' => true], ['text' => 'Pace', 'correct' => false], ['text' => 'Pitch', 'correct' => false], ['text' => 'Tone', 'correct' => false]]],
            ],
            'Movies' => [
                ['question' => 'Kurš režisors uzņēma "Jurassic Park"?', 'answers' => [['text' => 'Stīvens Spīlbergs', 'correct' => true], ['text' => 'Džordžs Lukass', 'correct' => false], ['text' => 'Džeims Kamerons', 'correct' => false], ['text' => 'Kristofers Nolans', 'correct' => false]]],
                ['question' => 'Kurš aktieris spēlē Iron Man?', 'answers' => [['text' => 'Roberts Daunijs juniors', 'correct' => true], ['text' => 'Kris Evans', 'correct' => false], ['text' => 'Hju Džekmens', 'correct' => false], ['text' => 'Ben Afleks', 'correct' => false]]],
                ['question' => 'Kurš no šiem ir supervaroņu filma?', 'answers' => [['text' => 'Black Panther', 'correct' => true], ['text' => 'Titanic', 'correct' => false], ['text' => 'The Shining', 'correct' => false], ['text' => 'The Matrix', 'correct' => false]]],
                ['question' => 'Kurš režisors uzņēma "Inception"?', 'answers' => [['text' => 'Kristofers Nolans', 'correct' => true], ['text' => 'Stīvens Spīlbergs', 'correct' => false], ['text' => 'Kventins Tarantino', 'correct' => false], ['text' => 'Pīters Džeksons', 'correct' => false]]],
                ['question' => 'Kurā filmā ir frāze "May the Force be with you"?', 'answers' => [['text' => 'Star Wars', 'correct' => true], ['text' => 'Star Trek', 'correct' => false], ['text' => 'Avatar', 'correct' => false], ['text' => 'The Matrix', 'correct' => false]]],
                ['question' => 'Kurš no šiem ir romantiskās filmas piemērs?', 'answers' => [['text' => 'Titanic', 'correct' => true], ['text' => 'Alien', 'correct' => false], ['text' => 'Die Hard', 'correct' => false], ['text' => 'Jurassic Park', 'correct' => false]]],
                ['question' => 'Kurš no šiem ir animācijas filma?', 'answers' => [['text' => 'Toy Story', 'correct' => true], ['text' => 'Jaws', 'correct' => false], ['text' => 'Se7en', 'correct' => false], ['text' => 'Gladiator', 'correct' => false]]],
                ['question' => 'Kurā filmā galvenais varonis ir Ķiršu kapteinis?', 'answers' => [['text' => 'Pirates of the Caribbean', 'correct' => true], ['text' => 'The Pirate Bay', 'correct' => false], ['text' => 'Hook', 'correct' => false], ['text' => 'Muppet Treasure Island', 'correct' => false]]],
                ['question' => 'Kurā filmā ir robots, kas kļūst draugs ar zēnu?', 'answers' => [['text' => 'Iron Giant', 'correct' => true], ['text' => 'The Terminator', 'correct' => false], ['text' => 'A.I. Artificial Intelligence', 'correct' => false], ['text' => 'Wall-E', 'correct' => false]]],
                ['question' => 'Kurā filma ir par futbola komandas treneri?', 'answers' => [['text' => 'Remember the Titans', 'correct' => true], ['text' => 'The Blind Side', 'correct' => false], ['text' => 'Space Jam', 'correct' => false], ['text' => 'Rocky', 'correct' => false]]],
                ['question' => 'Kurš no šiem ir zinātniskās fantastikas darbs?', 'answers' => [['text' => 'The Matrix', 'correct' => true], ['text' => 'Pride and Prejudice', 'correct' => false], ['text' => 'The Notebook', 'correct' => false], ['text' => 'Titanic', 'correct' => false]]],
                ['question' => 'Kurš aktieris spēlē Dzeku Sparrow?', 'answers' => [['text' => 'Džonijs Deps', 'correct' => true], ['text' => 'Lijsam Neesonam', 'correct' => false], ['text' => 'Marks Vebers', 'correct' => false], ['text' => 'Džordžs Klūnijs', 'correct' => false]]],
                ['question' => 'Kurā filmā ir zelta monēta un pirātu laikā?', 'answers' => [['text' => 'Pirates of the Caribbean', 'correct' => true], ['text' => 'Treasure Planet', 'correct' => false], ['text' => 'Hook', 'correct' => false], ['text' => 'Peter Pan', 'correct' => false]]],
                ['question' => 'Kurš no šiem ir Holivudas reālās dzīves filmas piemērs?', 'answers' => [['text' => 'Bohemian Rhapsody', 'correct' => true], ['text' => 'Toy Story', 'correct' => false], ['text' => 'Jumanji', 'correct' => false], ['text' => 'The Lion King', 'correct' => false]]],
                ['question' => 'Kurš no šiem ir supervaroņu filmu režisors?', 'answers' => [['text' => 'Kristofers Nolans', 'correct' => false], ['text' => 'Zaks Snajders', 'correct' => true], ['text' => 'Greta Gerwig', 'correct' => false], ['text' => 'James Cameron', 'correct' => false]]],
            ],
            'Science' => [
                ['question' => 'Kurš elements ir pirmais periodiskajā tabulā?', 'answers' => [['text' => 'Hidrogēns', 'correct' => true], ['text' => 'Helijs', 'correct' => false], ['text' => 'Litijs', 'correct' => false], ['text' => 'Ogļskābe', 'correct' => false]]],
                ['question' => 'Kurš process izmanto saules gaismu, lai ražotu pārtiku?', 'answers' => [['text' => 'Fotosintēze', 'correct' => true], ['text' => 'Fermentācija', 'correct' => false], ['text' => 'Izgarošana', 'correct' => false], ['text' => 'Kondensācija', 'correct' => false]]],
                ['question' => 'Kurš spēks velk objektus uz Zemi?', 'answers' => [['text' => 'Gravitācija', 'correct' => true], ['text' => 'Magnētisms', 'correct' => false], ['text' => 'Trieciena spēks', 'correct' => false], ['text' => 'Līme', 'correct' => false]]],
                ['question' => 'Kā sauc gaismas ātrumu vakuumā?', 'answers' => [['text' => 'c', 'correct' => true], ['text' => 'v', 'correct' => false], ['text' => 'g', 'correct' => false], ['text' => 's', 'correct' => false]]],
                ['question' => 'Kurš ķīmijas simbols ir O?', 'answers' => [['text' => 'Skābeklis', 'correct' => true], ['text' => 'Zelts', 'correct' => false], ['text' => 'Ogļskābe', 'correct' => false], ['text' => 'Dzelzs', 'correct' => false]]],
                ['question' => 'Kura viela parasti ir caurspīdīga un šķidra istabas temperatūrā?', 'answers' => [['text' => 'Ūdens', 'correct' => true], ['text' => 'Ametafors', 'correct' => false], ['text' => 'Stikls', 'correct' => false], ['text' => 'Smilts', 'correct' => false]]],
                ['question' => 'Kurš cilvēks izstrādāja relativitātes teoriju?', 'answers' => [['text' => 'Alberts Einšteins', 'correct' => true], ['text' => 'Isaacs Ņūtons', 'correct' => false], ['text' => 'Nikola Tesla', 'correct' => false], ['text' => 'Galileo Galilejs', 'correct' => false]]],
                ['question' => 'Kuras planētas virsmas ir gandrīz pilnīgi sastāvē no ledus?', 'answers' => [['text' => 'Neptūns', 'correct' => false], ['text' => 'Saturns', 'correct' => false], ['text' => 'Uranuss', 'correct' => true], ['text' => 'Venēra', 'correct' => false]]],
                ['question' => 'Kura vienība mēra temperatūru?', 'answers' => [['text' => 'Celsijs', 'correct' => true], ['text' => 'Volts', 'correct' => false], ['text' => 'Džouls', 'correct' => false], ['text' => 'Metri', 'correct' => false]]],
                ['question' => 'Kura struktūra mūsdienu cilvēkam nodrošina smadzeņu darbību?', 'answers' => [['text' => 'Smadzenes', 'correct' => true], ['text' => 'Sirds', 'correct' => false], ['text' => 'Aknas', 'correct' => false], ['text' => 'Plaušas', 'correct' => false]]],
                ['question' => 'Kurā stāvoklī materiāls satur jonus?', 'answers' => [['text' => 'Plazma', 'correct' => true], ['text' => 'Cietā', 'correct' => false], ['text' => 'Sausā', 'correct' => false], ['text' => 'Virs', 'correct' => false]]],
                ['question' => 'Kura no šīm ir svarīga dzīvības sastāvdaļa?', 'answers' => [['text' => 'Ūdens', 'correct' => true], ['text' => 'Dzelzs', 'correct' => false], ['text' => 'Pildspalva', 'correct' => false], ['text' => 'Betons', 'correct' => false]]],
                ['question' => 'Kura planēta ir pazīstama kā Zemes māsa?', 'answers' => [['text' => 'Venera', 'correct' => true], ['text' => 'Marss', 'correct' => false], ['text' => 'Merkurs', 'correct' => false], ['text' => 'Jupiters', 'correct' => false]]],
                ['question' => 'Kā sauc procesu, kad šķidrs materiāls kļūst par gāzi?', 'answers' => [['text' => 'Vārīšanās', 'correct' => true], ['text' => 'Kondensācija', 'correct' => false], ['text' => 'Sasaldēšana', 'correct' => false], ['text' => 'Kristalizācija', 'correct' => false]]],
                ['question' => 'Kurā no šīm vielām ir visvairāk enerģijas, ja to salīdzina?', 'answers' => [['text' => 'Aknu tauki', 'correct' => false], ['text' => 'Benzīns', 'correct' => true], ['text' => 'Ūdens', 'correct' => false], ['text' => 'Smiltis', 'correct' => false]]],
            ],
            'History' => [
                ['question' => 'Kurā gadā sākās Otrais pasaules karš?', 'answers' => [['text' => '1939', 'correct' => true], ['text' => '1914', 'correct' => false], ['text' => '1945', 'correct' => false], ['text' => '1920', 'correct' => false]]],
                ['question' => 'Kas bija pirmais Latvijas valdības prezidents?', 'answers' => [['text' => 'Jānis Čakste', 'correct' => true], ['text' => 'Kārlis Ulmanis', 'correct' => false], ['text' => 'Vaira Vīķe-Freiberga', 'correct' => false], ['text' => 'Valdis Zatlers', 'correct' => false]]],
                ['question' => 'Kas bija pirmais cilvēks uz Mēness?', 'answers' => [['text' => 'Nīls Ārmstrongs', 'correct' => true], ['text' => 'Bāz Oldrins', 'correct' => false], ['text' => 'Majkls Kolins', 'correct' => false], ['text' => 'Jūrijs Gagarins', 'correct' => false]]],
                ['question' => 'Kurā gadā Latvija atguva neatkarību no PSRS?', 'answers' => [['text' => '1991', 'correct' => true], ['text' => '1990', 'correct' => false], ['text' => '1989', 'correct' => false], ['text' => '1992', 'correct' => false]]],
                ['question' => 'Kurš atklāja Ameriku 1492. gadā?', 'answers' => [['text' => 'Kristofers Kolumbs', 'correct' => true], ['text' => 'Marco Polo', 'correct' => false], ['text' => 'Vasko da Gama', 'correct' => false], ['text' => 'Amerigo Vespucci', 'correct' => false]]],
                ['question' => 'Kurš bija Lielbritānijas premjers Otrā pasaules kara laikā?', 'answers' => [['text' => 'Vinstons Čērčils', 'correct' => true], ['text' => 'Nevils Čembers', 'correct' => false], ['text' => 'Tonijs Blērs', 'correct' => false], ['text' => 'Margareta Tečere', 'correct' => false]]],
                ['question' => 'Kurā gadā sākās Francijas revolūcija?', 'answers' => [['text' => '1789', 'correct' => true], ['text' => '1776', 'correct' => false], ['text' => '1812', 'correct' => false], ['text' => '1804', 'correct' => false]]],
                ['question' => 'Kurš bija Indijas neatkarības līderis?', 'answers' => [['text' => 'Mahātma Gandijs', 'correct' => true], ['text' => 'Nehru', 'correct' => false], ['text' => 'Čērčils', 'correct' => false], ['text' => 'Mandela', 'correct' => false]]],
                ['question' => 'Kas bija Romiešu impērijas centrālā pilsēta?', 'answers' => [['text' => 'Roma', 'correct' => true], ['text' => 'Atēnas', 'correct' => false], ['text' => 'Sparta', 'correct' => false], ['text' => 'Kairas', 'correct' => false]]],
                ['question' => 'Kurš bija slavenais pirātu izpildītājs filmu "Pirates of the Caribbean"?', 'answers' => [['text' => 'Džonijs Deps', 'correct' => true], ['text' => 'Leo DiKaprio', 'correct' => false], ['text' => 'Toms Henks', 'correct' => false], ['text' => 'Braiens Briezel', 'correct' => false]]],
                ['question' => 'Kurš imperators bija pazīstams kā „kvalitātes braucējs”?', 'answers' => [['text' => 'Nekādā', 'correct' => false], ['text' => 'Augusts', 'correct' => true], ['text' => 'Napoleons', 'correct' => false], ['text' => 'Čingishans', 'correct' => false]]],
                ['question' => 'Kurā gadsimtā notika Latvijas brīvības cīņas?', 'answers' => [['text' => '20. gadsimtā', 'correct' => true], ['text' => '19. gadsimtā', 'correct' => false], ['text' => '18. gadsimtā', 'correct' => false], ['text' => '21. gadsimtā', 'correct' => false]]],
                ['question' => 'Kurš bija pirmais ASV prezidents?', 'answers' => [['text' => 'Džordžs Vašingtons', 'correct' => true], ['text' => 'Abrahams Linkolns', 'correct' => false], ['text' => 'Tomas Džefersons', 'correct' => false], ['text' => 'Džons Adams', 'correct' => false]]],
                ['question' => 'Kurā gadsimtā parādījās prints?', 'answers' => [
                    ['text' => '15. gadsimtā', 'correct' => true],
                    ['text' => '12. gadsimtā', 'correct' => false],
                    ['text' => '18. gadsimtā', 'correct' => false],
                    ['text' => '20. gadsimtā', 'correct' => false],
                ]],
                ['question' => 'Kurš bija pirmais cilvēks, kurš krustojumā atklāja Ameriku?', 'answers' => [['text' => 'Kristofers Kolumbs', 'correct' => true], ['text' => 'Magelāns', 'correct' => false], ['text' => 'Vasko da Gama', 'correct' => false], ['text' => 'Marco Polo', 'correct' => false]]],
            ],
        ];
    }
}
