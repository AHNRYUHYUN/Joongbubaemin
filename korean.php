<?php
$servername = "localhost";
$username = "user";
$password = "12345";
$dbname = "sample";

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 음식 카테고리가 '한식'인 식당을 가져오는 SQL 쿼리
$sql = "SELECT * FROM 가게 WHERE 음식카테고리 = '한식'";
$result = $conn->query($sql);

// HTML 시작
echo '<div style="text-align: center; margin-bottom: 20px;"><h1 style="font-size: 36px;">메뉴</h1></div>';
echo '<div style="display: flex; flex-wrap: wrap; justify-content: center; margin: 80px;">'; // 여백을 추가하여 양쪽 사이드를 비워놓음

// 가져온 데이터를 출력
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // 각 가게에 대한 HTML 출력
        echo '<div style="margin: 10px; width: 45%; text-align: center;">';

// 이미지 경로를 데이터베이스에서 가져오거나 기본 이미지를 사용할 수 있도록 처리
switch ($row["가게이름"]) {
    case "곱도리탕":
        $imagePath = "곱도리탕.jpg"; // 곱도리탕에 대한 이미지 경로
        break;
    case "밥통령짜글이":
        $imagePath = "짜글이.jpg"; // 밥통령짜글이 대한 이미지 경로
        break;
    
}

// CSS 스타일을 추가하여 이미지 크기 일정하게 설정
echo '<div style="max-width: 80%; height: 300px; overflow: hidden; margin-left:50px; border-radius: 80px; border: 4px solid #000;">';
echo '<img src="' . $imagePath . '" alt="' . $row["가게이름"] . '" style="width: 100%; height: 100%; object-fit: cover;">';
echo '</div>';

// 나머지 정보 출력
echo '<p style="margin-top: 10px; font-weight: bold; font-size: 18px;">가게 이름: ' . $row["가게이름"] . '</p>';
echo '<p>음식 카테고리: ' . $row["음식카테고리"] . '<br>주소: ' . $row["주소"] . '</p>';
// 추가된 정보 출력 (존재 여부 확인 후 출력)
echo '<p>전화번호: ' . (isset($row["전화번호"]) ? $row["전화번호"] : '정보 없음') . '</p>';
echo '<p>최소 주문 금액: ' . (isset($row["최소구매금액"]) ? $row["최소구매금액"] . '원' : '정보 없음') . '</p>';
echo '<p>운영 시간: ' . (isset($row["운영시간"]) ? $row["운영시간"] : '정보 없음') . '</p>';
echo '<p>휴무일: ' . (isset($row["휴무일"]) ? $row["휴무일"] : '정보 없음') . '</p>';
echo '<p>배달 가능 지역: ' . (isset($row["배달지역"]) ? $row["배달지역"] : '정보 없음') . '</p>';

// 메뉴 페이지에 대한 링크 수정
echo '<a href="menu.php?store_id=' . $row["가게이름"] . '" style="text-decoration: none; color: #3498db; font-weight: bold;">메뉴보기</a>';
echo '</div>';
}
} else {
echo '<p style="text-align: center; font-size: 18px;">해당하는 식당이 없습니다.</p>';
}


// HTML 종료
echo '</div>';

// 데이터베이스 연결 종료
$conn->close();
?>
