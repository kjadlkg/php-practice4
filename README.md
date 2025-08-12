# php-practice4

이 프로젝트의 목표는 **CRUD**와 **OWASP 기반 보안 지식**을 확장하여 하나의 **완성형 웹사이트를 구현**하는 것입니다.

국내 최대 규모의 PHP 기반 커뮤니티 사이트인 **디시인사이드**를 참고하여, UI 구현을 시작으로 웹사이트 구조의 이해를 높였습니다.

또한, 사용자 편의성을 높이는 부가 기능들을 참고하여 본 프로젝트에 적용하였습니다.

## 📦개발 환경

- XAMPP v.3.3.0 (Apr 6th 2021)

## ✨구현된 주요 기능

- **회원 기능**
  - 로그인 / 회원가입 / 비밀번호 찾기
  - 내 정보 조회, 수정 / 회원탈퇴
  - 마이페이지 게시물·댓글 히스토리 및 검색
- **게시판 기능**
  - 게시물 조회, 작성, 수정, 삭제
  - 댓글 작성, 삭제 / 대댓글 작성
  - 게시물 추천·비추천, 신고, 공유, 검색
- **보안**
  - 비로그인 사용자 이용 대응
  - 캡차 이미지 적용
  - SQL Injection, CSRF 등 보안 취약점 대응책 마련

## 🚀실행 & 테스트 방법

### 1. 빠른 테스트

- **호스팅 사이트**: InfinityFree
- 🔗 [테스트 바로가기](https://kjadlkg.infy.uk)

### 2. 로컬 설치 (XAMPP)

1. **XAMPP 설치**
   ```
   https://www.apachefriends.org/download.html
   ```
2. **프로젝트 다운로드**
   ```bash
   git clone https://github.com/kjadlkg/php-practice4.git
   ```
   또는 ZIP 다운로드 후 `htdocs` 폴더에 복사
3. **htdocs에 배치**
   ```
   C:\xampp\htdocs
   ```
   또는 위 위치에서 `git clone` 실행
4. **MySQL 서버 실행 & phpMyAdmin 접속**
   ```
   http://localhost/phpmyadmin
   ```
5. **새 데이터베이스 생성 (예: test3)**
   - 다른 DB 이름을 사용하려면 `db.php`에서 `database` 값 변경
6. **db/test3.sql 파일 Import**

   - 생성한 DB에 `db/test3.sql` 파일을 Import

7. **브라우저 접속**
   ```
   http://localhost/php-practice4/main/
   ```

## 🔧추가 업데이트 예정

- [ ] 대댓글 표시 오류 개선
- [ ] 대댓글 CSS 적용
- [ ] 댓글 등록 + 추천 기능 구현
- [ ] 즐겨찾기 기능 구현
- [ ] 알림 기능 구현
