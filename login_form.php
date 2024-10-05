<!DOCTYPE html>
<html>
<head> 
    <meta charset="utf-8">
    <title>배달의 민족</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script>
        function checkLoginInput() {
            if (!document.login.id.value) {
                alert("아이디를 입력하세요");
                document.login.id.focus();
                return false;
            }

            if (!document.login.pass.value) {
                alert("비밀번호를 입력하세요");
                document.login.pass.focus();
                return false;
            }

            return true;
        }

        function checkSignupInput() {
         
            return true;
        }
    </script>
</head>
<body> 
    <h2 class="login_title">로그인</h2>
    <form name="login" method="post" action="login.php" onsubmit="return checkLoginInput()">             
        <ul class="login_form">
            <li>
                <span class="col1">아이디</span>
                <span class="col2"><input type="text" name="id" placeholder="아이디"></span>
            </li>   
            <li>            
                <span class="col1">비밀번호</span>
                <span class="col2"><input type="password" name="pass" placeholder="비밀번호"></span>
            </li>
            <li><button type="submit">로그인</button></li>
        </ul>   
                 
    </form>

    <form name="signup" method="post" action="add.php" onsubmit="return checkSignupInput()">               
        <ul class="login_form">
            <li style="display: inline;"><button type="submit">회원가입</button></li>
        </ul>
    </form>
</body>
</html>
