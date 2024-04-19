<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';
require_once __DIR__ . '/../../../tool/php/ratingStars.php';
require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';
    // Get the itemsPerPage and page parameters from the AJAX request
    $itemsPerPage = isset($_GET['itemsPerPage']) ? intval($_GET['itemsPerPage']) : 10;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

    // Calculate the offset
    $offset = ($page - 1) * $itemsPerPage;

    // Connect to your database
    $conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);

    // Prepare the SQL query
    $stmt = mysqli_prepare($conn, "WITH RankedBooks AS (
  SELECT book.id, book.name,
         author.authorName,
         fileCopy.price AS filePrice,
         physicalCopy.price AS physicalPrice,
         book.imagePath AS pic,
         book.avgRating AS star,
         eventapply.eventID,
         COALESCE(eventdiscount.discount, 0) AS discount,
         ROW_NUMBER() OVER (PARTITION BY book.id ORDER BY discount DESC) AS discount_rank
  FROM book
  INNER JOIN author ON book.id = author.bookID
  INNER JOIN fileCopy ON book.id = fileCopy.id
  INNER JOIN physicalCopy ON book.id = physicalCopy.id
  LEFT JOIN eventapply ON book.id = eventapply.bookID
  LEFT JOIN eventdiscount ON eventapply.eventID = eventdiscount.ID
)
SELECT *
FROM RankedBooks
WHERE discount_rank = 1 LIMIT ? OFFSET ?");

    // Bind the limit and offset parameters
    mysqli_stmt_bind_param($stmt, 'ii', $itemsPerPage, $offset);

    // Execute the query
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    // Fetch the books
    $books = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    echo json_encode($books);
?>