
<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

if (!check_session() || (check_session() && $_SESSION['type'] !== 'admin')) {
      http_response_code(403);
      echo json_encode(['error' => 'Not authorized!']);
      exit;
}

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';
require_once __DIR__ . '/../../../tool/php/check_https.php';

// Include Composer's autoloader
require_once __DIR__ . '/../../../vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (
            isset($_GET['entry']) &&
            isset($_GET['offset']) &&
            isset($_GET['status']) &&
            isset($_GET['search']) &&
            isset($_GET['category']) &&
            isset($_GET['start']) &&
            isset($_GET['end']) &&
            isset($_GET['author']) &&
            isset($_GET['publisher'])
      ) {
            try {
                  $entry = sanitize(rawurldecode($_GET['entry']));
                  $offset = sanitize(rawurldecode($_GET['offset']));
                  $status = filter_var(sanitize(rawurldecode($_GET['status'])), FILTER_VALIDATE_BOOLEAN);
                  $search = sanitize(rawurldecode($_GET['search']));
                  $category = sanitize(rawurldecode($_GET['category']));
                  $start = sanitize(rawurldecode($_GET['start']));
                  $end = sanitize(rawurldecode($_GET['end']));
                  $author = sanitize(rawurldecode($_GET['author']));
                  $publisher = sanitize(rawurldecode($_GET['publisher']));

                  if (!$entry) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing number of entries of books!']);
                        exit;
                  } else if (!is_numeric($entry) || is_nan($entry) || $entry < 0) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Number of entries of books invalid!']);
                        exit;
                  }

                  if (!$offset) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing book list number!']);
                        exit;
                  } else if (!is_numeric($offset) || is_nan($offset) || $offset <= 0) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Book list number invalid!']);
                        exit;
                  }

                  if (!$start) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing start date!']);
                        exit;
                  }

                  if (!$end) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing end date!']);
                        exit;
                  }

                  $startDT = new DateTime($start, new DateTimeZone($_ENV['TIMEZONE']));
                  $startDT->setTime(0, 0, 0); // Set time to 00:00:00
                  $endDT = new DateTime($end, new DateTimeZone($_ENV['TIMEZONE']));
                  $endDT->setTime(0, 0, 0); // Set time to 00:00:00
                  $currentDate = new DateTime('now', new DateTimeZone($_ENV['TIMEZONE']));
                  $currentDate->setTime(0, 0, 0); // Set time to 00:00:00

                  if ($startDT > $currentDate) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Start date must be before or the same day as today!']);
                        exit;
                  }

                  if ($endDT > $currentDate) {
                        http_response_code(400);
                        echo json_encode(['error' => 'End date must be before or the same day as today!']);
                        exit;
                  }

                  if ($startDT > $endDT) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Start date must be before or the same day as end date!']);
                        exit;
                  }

                  $queryResult = [];
                  $isbnSearch = '%' . str_replace('-', '', $search) . '%';
                  $search = '%' . $search . '%';
                  $offset = ($offset - 1) * $entry;
                  $category = '%' . $category . '%';
                  $author = '%' . $author . '%';
                  $publisher = '%' . $publisher . '%';

                  // Connect to MySQL
                  $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

                  // Check connection
                  if (!$conn) {
                        http_response_code(500);
                        echo json_encode(['error' => 'MySQL Connection Failed!']);
                        exit;
                  }

                  $stmt = null;

                  $stmt = $conn->prepare("SELECT distinct book.id,book.name,book.edition,book.isbn,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath from book join (SELECT book.id as bookID,coalesce(sum(combined.totalSold),0) as totalSold from (
                        select bookID,sum(amount) as totalSold from physicalOrderContain join customerOrder on customerOrder.id=physicalOrderContain.orderID where customerOrder.status=true and date(purchaseTime)>=? and date(purchaseTime)<=? group by bookID
                        union
                        select bookID,count(*) as totalSold from fileOrderContain join customerOrder on customerOrder.id=fileOrderContain.orderID where customerOrder.status=true and date(purchaseTime)>=? and date(purchaseTime)<=? group by bookID
                        ) as combined
                        right join book on book.id=combined.bookID
                        group by book.id order by sum(totalSold) desc) as result on book.id=result.bookID
                        join author on author.bookID=book.id join belong on belong.bookID=book.id join category on category.id=belong.categoryID
                        where book.status=? and (book.name like ? or book.isbn like ?) and book.publisher like ? and author.authorName like ? and category.name like ?
                        order by result.totalSold desc,book.name,book.edition
                        limit ? offset ?;");

                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `SELECT distinct book.id,book.name,book.edition,book.isbn,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath from book join (SELECT book.id as bookID,coalesce(sum(combined.totalSold),0) as totalSold from (
                        select bookID,sum(amount) as totalSold from physicalOrderContain join customerOrder on customerOrder.id=physicalOrderContain.orderID where customerOrder.status=true and date(purchaseTime)>=? and date(purchaseTime)<=? group by bookID
                        union
                        select bookID,count(*) as totalSold from fileOrderContain join customerOrder on customerOrder.id=fileOrderContain.orderID where customerOrder.status=true and date(purchaseTime)>=? and date(purchaseTime)<=? group by bookID
                        ) as combined
                        right join book on book.id=combined.bookID
                        group by book.id order by sum(totalSold) desc) as result on book.id=result.bookID
                        join author on author.bookID=book.id join belong on belong.bookID=book.id join category on category.id=belong.categoryID
                        where book.status=? and (book.name like ? or book.isbn like ?) and book.publisher like ? and author.authorName like ? and category.name like ?
                        order by result.totalSold desc,book.name,book.edition
                        limit ? offset ?;` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('ssssisssssii', $start, $end, $start, $end, $status, $search, $isbnSearch,  $publisher, $author, $category, $entry, $offset);

                  // if ($category === '%%') {
                  //             $stmt = $conn->prepare('select distinct book.id,book.name,book.edition,book.isbn,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath
                  //       from book join author on book.id=author.bookID
                  //       where book.status=? and (book.name like ? or book.isbn like ?) and book.publisher like ? and author.authorName like ?
                  //       order by book.name,book.id limit ? offset ?');
                  //       if (!$stmt) {
                  //             http_response_code(500);
                  //             echo json_encode(['error' => 'Query `select distinct book.id,book.name,book.edition,book.isbn,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath
                  // from book join author on book.id=author.bookID
                  // where book.status=? and (book.name like ? or book.isbn like ?) and book.publisher like ? and author.authorName like ?
                  // order by book.name,book.id limit ? offset ?` preparation failed!']);
                  //             $conn->close();
                  //             exit;
                  //       }
                  //       $stmt->bind_param('issssii', $status, $search, $isbnSearch, $publisher, $author, $entry, $offset);
                  // } else {
                  //       $stmt = $conn->prepare('select distinct book.id,book.name,book.edition,book.isbn,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath
                  // from book join author on book.id=author.bookID
                  // join belong on belong.bookID=book.id
                  // join category on category.id=belong.categoryID
                  // where book.status=? and (book.name like ? or book.isbn like ?) and book.publisher like ? and author.authorName like ? and category.name like ?
                  // order by book.name,book.id limit ? offset ?');
                  //       if (!$stmt) {
                  //             http_response_code(500);
                  //             echo json_encode(['error' => 'Query `select distinct book.id,book.name,book.edition,book.isbn,book.avgRating,book.publisher,book.publishDate,book.description,book.imagePath
                  // from book join author on book.id=author.bookID
                  // join belong on belong.bookID=book.id
                  // join category on category.id=belong.categoryID
                  // where book.status=? and (book.name like ? or book.isbn like ?) and book.publisher like ? and author.authorName like ? and category.name like ?
                  // order by book.name,book.id limit ? offset ?` preparation failed!']);
                  //             $conn->close();
                  //             exit;
                  //       }
                  //       $stmt->bind_param('isssssii', $status, $search, $isbnSearch,  $publisher, $author, $category, $entry, $offset);
                  // }
                  $isSuccess = $stmt->execute();

                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                  } else {
                        $result = $stmt->get_result();

                        $idx = 0;
                        while ($row = $result->fetch_assoc()) {
                              $host = $_SERVER['HTTP_HOST'];
                              $row['imagePath'] = "src=\"" . (isSecure() ? 'https' : 'http') . "://$host/data/book/" . normalizeURL(rawurlencode($row['imagePath'])) . "\"";
                              $row['edition'] = convertToOrdinal($row['edition']);
                              $row['isbn'] = formatISBN($row['isbn']);
                              $row['publishDate'] = MDYDateFormat($row['publishDate']);
                              $row['description'] = $row['description'] ? $row['description'] : 'N/A';
                              $queryResult[] = $row;

                              $id = $row['id'];

                              $sub_stmt = $conn->prepare('select (exists(select * from customerOrder join fileOrderContain on fileOrderContain.orderID=customerOrder.id where customerOrder.status=true and fileOrderContain.bookID=?) 
    or exists(select * from customerOrder join physicalOrderContain on physicalOrderContain.orderID=customerOrder.id where customerOrder.status=true and physicalOrderContain.bookID=?)) as result');
                              if (!$sub_stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select (exists(select * from customerOrder join fileOrderContain on fileOrderContain.orderID=customerOrder.id where customerOrder.status=true and fileOrderContain.bookID=?) 
    or exists(select * from customerOrder join physicalOrderContain on physicalOrderContain.orderID=customerOrder.id where customerOrder.status=true and physicalOrderContain.bookID=?)) as result` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $sub_stmt->bind_param('ss', $id, $id);
                              $isSuccess = $sub_stmt->execute();
                              if (!$isSuccess) {
                                    http_response_code(500);
                                    echo json_encode(['error' => $stmt->error]);
                                    $sub_stmt->close();
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $sub_result = $sub_stmt->get_result();
                              $sub_result = $sub_result->fetch_assoc();
                              $queryResult[$idx]['can_delete'] = !$sub_result['result'];

                              $sub_stmt = $conn->prepare('select authorName from author where bookID=? order by authorName,authorIdx');
                              if (!$sub_stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select authorName from author where bookID=? order by authorName,authorIdx` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $sub_stmt->bind_param('s', $id);
                              $isSuccess = $sub_stmt->execute();
                              if (!$isSuccess) {
                                    http_response_code(500);
                                    echo json_encode(['error' => $sub_stmt->error]);
                                    $sub_stmt->close();
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $sub_result = $sub_stmt->get_result();
                              if ($sub_result->num_rows === 0) {
                                    $queryResult[$idx]['author'] = [];
                              } else {
                                    while ($sub_row = $sub_result->fetch_assoc()) {
                                          $queryResult[$idx]['author'][] = $sub_row['authorName'];
                                    }
                              }
                              $sub_stmt->close();

                              $sub_stmt = $conn->prepare('select category.name,category.description from category join belong on belong.categoryID=category.id where belong.bookID=? order by category.name,category.id');
                              if (!$sub_stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select category.name,category.description from category join belong on belong.categoryID=category.id where belong.bookID=? order by category.name,category.id` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $sub_stmt->bind_param('s', $id);
                              $isSuccess = $sub_stmt->execute();
                              if (!$isSuccess) {
                                    http_response_code(500);
                                    echo json_encode(['error' => $sub_stmt->error]);
                                    $sub_stmt->close();
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $sub_result = $sub_stmt->get_result();
                              if ($sub_result->num_rows === 0) {
                                    $queryResult[$idx]['category'] = [];
                              } else {
                                    while ($sub_row = $sub_result->fetch_assoc()) {
                                          $temp = [];
                                          $temp['name'] = $sub_row['name'];
                                          $temp['description'] = $sub_row['description'];
                                          $queryResult[$idx]['category'][] = $temp;
                                    }
                              }
                              $sub_stmt->close();

                              $sub_stmt = $conn->prepare('select price,inStock from physicalCopy where id=?');
                              if (!$sub_stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select price,inStock from physicalCopy where id=?` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $sub_stmt->bind_param('s', $id);
                              $isSuccess = $sub_stmt->execute();
                              if (!$isSuccess) {
                                    http_response_code(500);
                                    echo json_encode(['error' => $sub_stmt->error]);
                                    $sub_stmt->close();
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $sub_result = $sub_stmt->get_result();
                              if ($sub_result->num_rows === 0) {
                                    $queryResult[$idx]['physicalCopy'] = [];
                              } else if ($sub_result->num_rows === 1) {
                                    while ($sub_row = $sub_result->fetch_assoc()) {
                                          $queryResult[$idx]['physicalCopy']['price'] = $sub_row['price'] ? "\${$sub_row['price']}" : "N/A";
                                          $queryResult[$idx]['physicalCopy']['inStock'] = $sub_row['inStock'] ? $sub_row['inStock'] : "N/A";
                                    }
                              }
                              $sub_stmt->close();

                              $sub_stmt = $conn->prepare('select price,filePath from fileCopy where id=?');
                              if (!$sub_stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select price,filePath from fileCopy where id=?` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $sub_stmt->bind_param('s', $id);
                              $isSuccess = $sub_stmt->execute();
                              if (!$isSuccess) {
                                    http_response_code(500);
                                    echo json_encode(['error' => $sub_stmt->error]);
                                    $sub_stmt->close();
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $sub_result = $sub_stmt->get_result();
                              if ($sub_result->num_rows === 0) {
                                    $queryResult[$idx]['fileCopy'] = [];
                              } else if ($sub_result->num_rows === 1) {
                                    while ($sub_row = $sub_result->fetch_assoc()) {
                                          $sub_row['filePath'] = $sub_row['filePath'] ? "href=\"" . (isSecure() ? 'https' : 'http') . "://$host/data/book/" . normalizeURL(rawurlencode($sub_row['filePath'])) . "\"" : '';

                                          $queryResult[$idx]['fileCopy']['price'] = $sub_row['price'] ? "\${$sub_row['price']}" : "N/A";
                                          $queryResult[$idx]['fileCopy']['filePath'] = $sub_row['filePath'];
                                    }
                              }
                              $sub_stmt->close();

                              $sub_stmt = $conn->prepare("select sum(totalSold) as finalTotalSold from (
                              select bookID,sum(amount) as totalSold from physicalOrderContain join customerOrder on customerOrder.id=physicalOrderContain.orderID where customerOrder.status=true and date(purchaseTime)>=? and date(purchaseTime)<=? group by bookID
                              union
                              select bookID,count(*) as totalSold from fileOrderContain join customerOrder on customerOrder.id=fileOrderContain.orderID where customerOrder.status=true and date(purchaseTime)>=? and date(purchaseTime)<=? group by bookID
                              ) as combined where bookID=? group by bookID;");
                              if (!$sub_stmt) {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'Query `select sum(totalSold) as finalTotalSold from (
                              select bookID,sum(amount) as totalSold from physicalOrderContain join customerOrder on customerOrder.id=physicalOrderContain.orderID where customerOrder.status=true and date(purchaseTime)>=? and date(purchaseTime)<=? group by bookID
                              union
                              select bookID,count(*) as totalSold from fileOrderContain join customerOrder on customerOrder.id=fileOrderContain.orderID where customerOrder.status=true and date(purchaseTime)>=? and date(purchaseTime)<=? group by bookID
                              ) as combined where bookID=? group by bookID;` preparation failed!']);
                                    $conn->close();
                                    exit;
                              }
                              $sub_stmt->bind_param('sssss', $start, $end, $start, $end, $id);
                              $isSuccess = $sub_stmt->execute();
                              if (!$isSuccess) {
                                    http_response_code(500);
                                    echo json_encode(['error' => $sub_stmt->error]);
                                    $sub_stmt->close();
                                    $stmt->close();
                                    $conn->close();
                                    exit;
                              }
                              $sub_result = $sub_stmt->get_result();
                              if ($sub_result->num_rows === 1)
                                    $queryResult[$idx]['totalSold'] = $sub_result->fetch_assoc()['finalTotalSold'];
                              else
                                    $queryResult[$idx]['totalSold'] = 0;
                              $sub_stmt->close();

                              $idx++;
                        }
                  }
                  $stmt->close();

                  // if ($category === '%%') {
                  //       $stmt = $conn->prepare('select count(distinct book.id) as totalBook
                  // from book join author on book.id=author.bookID
                  // where book.status=? and (book.name like ? or book.isbn like ?) and book.publisher like ? and author.authorName like ?');
                  //       if (!$stmt) {
                  //             http_response_code(500);
                  //             echo json_encode(['error' => 'Query `select count(distinct book.id) as totalBook
                  // from book join author on book.id=author.bookID
                  // where book.status=? and (book.name like ? or book.isbn like ?) and book.publisher like ? and author.authorName like ?` preparation failed!']);
                  //             $conn->close();
                  //             exit;
                  //       }
                  //       $stmt->bind_param('issss', $status, $search, $isbnSearch, $publisher, $author);
                  // } else {
                  $stmt = $conn->prepare('select count(distinct book.id) as totalBook
                  from book join author on book.id=author.bookID
                  join belong on belong.bookID=book.id
                  join category on category.id=belong.categoryID
                  where book.status=? and (book.name like ? or book.isbn like ?) and book.publisher like ? and author.authorName like ? and category.name like ?');
                  if (!$stmt) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Query `select count(distinct book.id) as totalBook
                  from book join author on book.id=author.bookID
                  join belong on belong.bookID=book.id
                  join category on category.id=belong.categoryID
                  where book.status=? and (book.name like ? or book.isbn like ?) and book.publisher like ? and author.authorName like ? and category.name like ?` preparation failed!']);
                        $conn->close();
                        exit;
                  }
                  $stmt->bind_param('isssss', $status, $search, $isbnSearch, $publisher, $author, $category);
                  // }
                  $isSuccess = $stmt->execute();
                  if (!$isSuccess) {
                        http_response_code(500);
                        echo json_encode(['error' => $stmt->error]);
                        $stmt->close();
                        $conn->close();
                        exit;
                  } else {
                        $result = $stmt->get_result();
                        $result = $result->fetch_assoc();
                        $totalEntries = $result['totalBook'];
                  }
                  $stmt->close();

                  echo json_encode(['query_result' => [$queryResult, $totalEntries]]);

                  // Close connection
                  $conn->close();
            } catch (Exception $e) {
                  http_response_code(500);
                  echo json_encode(['error' => $e->getMessage()]);
            }
      } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data received!']);
      }
} else {
      http_response_code(400);
      echo json_encode(['error' => 'Invalid request method!']);
}
?>