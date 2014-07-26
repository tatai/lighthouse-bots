<?php
require __DIR__ . '/src/Thros/Autoloader.php';
require __DIR__ . '/src/AStar/Autoloader.php';
require __DIR__ . '/src/Lighthouse/Autoloader.php';
Thros\Autoloader::register();
AStar\Autoloader::register();
Lighthouse\Autoloader::register();

use \AStar\HeuristicCostCalculator;
use \AStar\Neighbors\NeighborsFree;

$bot = new Thros\Bot(
	new AStar\Engine(new AStar\HeuristicCostCalculator(), new AStar\Neighbors\NeighborsFree()),
	new Lighthouse\Command\Controller(
		fopen('php://stdin', 'r'),
		fopen('php://stdout', 'w'),
		((isset($argv[1]) && $argv[1] == 'debug') ? fopen('php://stderr', 'w') : null)
	)
);
$bot->fight();
