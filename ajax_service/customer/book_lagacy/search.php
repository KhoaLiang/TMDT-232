<?php
require_once __DIR__ . '/../../../tool/php/session_check.php';

require_once __DIR__ . '/../../../tool/php/sanitizer.php';
require_once __DIR__ . '/../../../config/db_connection.php';
require_once __DIR__ . '/../../../tool/php/converter.php';
require_once __DIR__ . '/../../../tool/php/formatter.php';
$search = $_GET['query'];

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_database, $db_port);
// Query the database for books in the selected category
// This is a simplified example, you should use prepared statements to prevent SQL injection
//$result = $conn->query("SELECT category.id FROM category WHERE category.id = '$category'");
$result = $conn->query("WITH SearchBooks AS (
WITH RankedBooks AS (
  SELECT book.id, book.name, book.isbn, book.publisher,
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
WHERE discount_rank = 1
)
SELECT *
FROM SearchBooks
WHERE SearchBooks.name LIKE  '%$search%' or SearchBooks.authorName LIKE '%$search%' or SearchBooks.isbn LIKE '%$search%'or SearchBooks.publisher LIKE '%$search%'");
//             else{$result = $conn->query("Select book.id, book.name, author.authorName, fileCopy.price as filePrice, physicalCopy.price as physicalPrice, book.imagePath as pic,  belong.categoryID, book.avgRating as star, category.name as categoryNAME from 
// book inner join author on book.id = author.bookID
//             join fileCopy on book.id = fileCopy.id
//             join physicalCopy on book.id = physicalCopy.id
//             join belong on book.id = belong.bookID
//             join category on belong.categoryID = category.id
//             Where category.id = '$category'");}


$books = array();
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

// Return the books as a JSON response
echo json_encode($books);
?>