<?php

class IplFixture
{
  /**
   * Name of the server
   * 
   * @var string
   */
  private $servername = "localhost";

  /**
   * MySQL username
   * 
   * @var string
   */
  private $username = "assignmentuser";

  /**
   * MySQL password
   * 
   * @var string
   */
  private $password = "Hello@123";

  /**
   * Name of your database
   * 
   * @var string
   */
  private $dbname = "ipl_database";

  /**
   * MySQLi connection object for the database.
   *
   * @var mysqli
   */
  public $conn;

  /**
   * Constructor to initialize the database connection.
   * 
   * @return void
   */
  public function __construct()
  {
    $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
    
    // Check the connection
    if ($this->conn->connect_error) {
      die("Connection failed: " . $this->conn->connect_error);
    }

    // Create Database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS $this->dbname";
    if (!$this->conn->query($sql) === TRUE) {
      die("Error creating database: " . $this->conn->error);
    }

    // Select the database
    $this->conn->select_db($this->dbname);
  }

  /**
   * Function to create the necessary tables.
   * 
   * @return void
   */
  public function createTables()
  {
    // Table for teams details
    $sql = "CREATE TABLE IF NOT EXISTS Teams (
      team_id INT AUTO_INCREMENT PRIMARY KEY, 
      team_name VARCHAR(100) NOT NULL, 
      captain VARCHAR(100) NOT NULL
    )";
    if (!$this->conn->query($sql) === TRUE) {
      die("Error creating table 'Teams': " . $this->conn->error);
    }

    // Table for match details
    $sql = "CREATE TABLE IF NOT EXISTS Matches (
      match_num INT AUTO_INCREMENT PRIMARY KEY,
      venue VARCHAR(100) NOT NULL,
      date DATE NOT NULL,
      team1_id INT NOT NULL,
      team2_id INT NOT NULL,
      toss_winner INT NOT NULL,
      match_winner INT NOT NULL,
      FOREIGN KEY (team1_id) REFERENCES Teams(team_id),
      FOREIGN KEY (team2_id) REFERENCES Teams(team_id),
      FOREIGN KEY (toss_winner) REFERENCES Teams(team_id),
      FOREIGN KEY (match_winner) REFERENCES Teams(team_id)
    )";
    if (!$this->conn->query($sql) === TRUE) {
      die("Error creating table 'Matches': " . $this->conn->error);
    }
  }

  /**
   * Function to insert data into tables.
   * 
   * @return void
   */
  public function insertData()
  {
    // Teams data
    $teams = [
      ['team_name' => 'Mumbai Indians', 'captain' => 'Rohit Sharma'],
      ['team_name' => 'Chennai Super Kings', 'captain' => 'MS Dhoni'],
      ['team_name' => 'Royal Challengers Bangalore', 'captain' => 'Virat Kohli'],
      ['team_name' => 'Kolkata Knight Riders', 'captain' => 'Shreyas Iyer'],
      ['team_name' => 'Delhi Capitals', 'captain' => 'Rishabh Pant'],
      ['team_name' => 'Gujarat Titans', 'captain' => 'Hardik Pandya'],
      ['team_name' => 'Lucknow Super Giants', 'captain' => 'KL Rahul'],
      ['team_name' => 'Punjab Kings', 'captain' => 'Shikhar Dhawan'],
      ['team_name' => 'Rajasthan Royals', 'captain' => 'Sanju Samson'],
      ['team_name' => 'Sunrisers Hyderabad', 'captain' => 'Aiden Markram']
    ];

    foreach ($teams as $team) {
      // Check if the team already exists
      $sql = "SELECT * FROM Teams WHERE team_name = '" . $team['team_name'] . "'";
      $result = $this->conn->query($sql);

      // If team doesn't exist, insert it
      if ($result->num_rows == 0) {
        $sql = "INSERT INTO Teams (team_name, captain) VALUES ('" . $team['team_name'] . "', '" . $team['captain'] . "')";
        if (!$this->conn->query($sql) === TRUE) {
          die("Error inserting team " . $team['team_name'] . ": " . $this->conn->error);
        }
      }
    }

    // Matches data
    $matches = [
      ['venue' => 'Wankhede Stadium', 'date' => '2024-04-01', 'team1_id' => 1, 'team2_id' => 2, 'toss_winner' => 1, 'match_winner' => 1],
      ['venue' => 'Chepauk Stadium', 'date' => '2024-04-02', 'team1_id' => 2, 'team2_id' => 3, 'toss_winner' => 2, 'match_winner' => 2],
      ['venue' => 'M. Chinnaswamy Stadium', 'date' => '2024-04-03', 'team1_id' => 3, 'team2_id' => 4, 'toss_winner' => 3, 'match_winner' => 4],
      ['venue' => 'Eden Gardens', 'date' => '2024-04-04', 'team1_id' => 4, 'team2_id' => 5, 'toss_winner' => 4, 'match_winner' => 5],
      ['venue' => 'Wankhede Stadium', 'date' => '2024-04-05', 'team1_id' => 5, 'team2_id' => 6, 'toss_winner' => 5, 'match_winner' => 6]
    ];

    // Insert Matches data with duplicate check
    foreach ($matches as $match) {
      // Check if the match already exists
      $sql = "SELECT * FROM Matches WHERE venue = '" . $match['venue'] . "' AND date = '" . $match['date'] . "' AND team1_id = " . $match['team1_id'] . " AND team2_id = " . $match['team2_id'];
      $result = $this->conn->query($sql);

      // If match doesn't exist, insert it
      if ($result->num_rows == 0) {
        $sql = "INSERT INTO Matches (venue, date, team1_id, team2_id, toss_winner, match_winner) 
                VALUES ('" . $match['venue'] . "', '" . $match['date'] . "', " . $match['team1_id'] . ", " . $match['team2_id'] . ", " . $match['toss_winner'] . ", " . $match['match_winner'] . ")";
        if (!$this->conn->query($sql) === TRUE) {
          die("Error inserting match: " . $this->conn->error);
        }
      }
    }
  }

  /**
   * Function to display the match details.
   * 
   * @return void
   */
  public function showData()
  {
    // SQL query to fetch the required data from the Matches table and join with Teams table to get team names and captains
    $sql = "SELECT 
                m.match_num,
                m.date,
                m.venue,
                t1.team_name AS team1_name,
                t2.team_name AS team2_name,
                t1.captain AS captain1_name,
                t2.captain AS captain2_name,
                toss_winner.team_name AS toss_winner_name,
                match_winner.team_name AS match_winner_name
            FROM Matches m
            JOIN Teams t1 ON m.team1_id = t1.team_id
            JOIN Teams t2 ON m.team2_id = t2.team_id
            JOIN Teams toss_winner ON m.toss_winner = toss_winner.team_id
            JOIN Teams match_winner ON m.match_winner = match_winner.team_id";

    // Execute the query
    $result = $this->conn->query($sql);

    // Check if there are any results
    if ($result->num_rows > 0) {
      // Output the data
      echo "<table border='1'>";
      echo "<tr>
                <th>Match Number</th>
                <th>Date</th>
                <th>Venue</th>
                <th>Team 1</th>
                <th>Team 2</th>
                <th>Captain of Team 1</th>
                <th>Captain of Team 2</th>
                <th>Toss Winner</th>
                <th>Match Winner</th>
              </tr>";

      // Fetch and display the rows
      while ($row = $result->fetch_assoc()) {
        echo "<tr>
                    <td>" . $row['match_num'] . "</td>
                    <td>" . $row['date'] . "</td>
                    <td>" . $row['venue'] . "</td>
                    <td>" . $row['team1_name'] . "</td>
                    <td>" . $row['team2_name'] . "</td>
                    <td>" . $row['captain1_name'] . "</td>
                    <td>" . $row['captain2_name'] . "</td>
                    <td>" . $row['toss_winner_name'] . "</td>
                    <td>" . $row['match_winner_name'] . "</td>
                  </tr>";
      }
      echo "</table>";
    } else {
      // No records found
      echo "No matches found.";
    }
  }

  /**
   * Destructor to close the database connection.
   */
  public function __destruct()
  {
    $this->conn->close();
  }
}

// Instantiate the IplFixture object and execute the methods
$ipl24 = new IplFixture();
$ipl24->createTables();
$ipl24->insertData();
$ipl24->showData();

