<?php
header('Content-Type: text/html; charset=UTF-8');

$host     = 'localhost';
$dbname   = 'world';
$username = 'lab5_user';
$password = 'password123';
$country = isset($_GET['country']) ? trim($_GET['country']) : '';
$lookup  = isset($_GET['lookup']) ? trim($_GET['lookup']) : '';

if ($country === '') {
    echo '<p class="message">No country provided. Please enter a country name.</p>';
    exit;
}

// Set up PDO connection
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $conn = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    echo '<p class="message error">Database connection failed.</p>';
    // You can log $e->getMessage() in a real app
    exit;
}

// If lookup=cities, do the JOIN on cities + countries
if (strtolower($lookup) === 'cities') {
    /*
      Example based on your lab query:

      SELECT c.id, c.name as city, c.country_code, cs.name as country, c.population 
      FROM cities c
      JOIN countries cs ON c.country_code = cs.code 
      WHERE c.population > 6300000;
    */

    $sql = "
        SELECT 
            c.name      AS city_name,
            c.district  AS district,
            c.population AS population
        FROM cities c
        JOIN countries cs ON c.country_code = cs.code
        WHERE cs.name LIKE :country
        ORDER BY c.name ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute(['country' => '%' . $country . '%']);
    $cities = $stmt->fetchAll();

    if (!$cities) {
        echo '<p class="message">No cities found for that country.</p>';
        exit;
    }

    echo '<table class="result-table">';
    echo '<caption>Cities in matching countries</caption>';
    echo '
        <thead>
          <tr>
            <th>Name</th>
            <th>District</th>
            <th>Population</th>
          </tr>
        </thead>
        <tbody>
    ';

    foreach ($cities as $city) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($city['city_name']) . '</td>';
        echo '<td>' . htmlspecialchars($city['district']) . '</td>';
        echo '<td>' . number_format((int)$city['population']) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    exit;
}


$sql = "
    SELECT 
        name,
        continent,
        independence_year,
        head_of_state
    FROM countries
    WHERE name LIKE :country
    ORDER BY name ASC
";

$stmt = $conn->prepare($sql);
$stmt->execute(['country' => '%' . $country . '%']);
$countries = $stmt->fetchAll();

if (!$countries) {
    echo '<p class="message">No countries found matching that name.</p>';
    exit;
}

echo '<table class="result-table">';
echo '<caption>Country details</caption>';
echo '
    <thead>
      <tr>
        <th>Name</th>
        <th>Continent</th>
        <th>Independence Year</th>
        <th>Head of State</th>
      </tr>
    </thead>
    <tbody>
';

foreach ($countries as $row) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($row['name']) . '</td>';
    echo '<td>' . htmlspecialchars($row['continent']) . '</td>';
    echo '<td>' . htmlspecialchars($row['independence_year']) . '</td>';
    echo '<td>' . htmlspecialchars($row['head_of_state']) . '</td>';
    echo '</tr>';
}

echo '</tbody></table>';