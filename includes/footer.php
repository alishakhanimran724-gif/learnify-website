<?php
// ===== FOOTER COMPONENT =====
// File: includes/footer.php
?>
<footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3><i class="fas fa-graduation-cap"></i> Learnify</h3>
                <p>Expert teachers explaining concepts with clear examples and step-by-step solutions.</p>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="classes.php">Classes</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="privacy.php">Privacy Policy</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Classes</h4>
                <ul>
                    <li><a href="search.php?class_id=9">Class 9</a></li>
                    <li><a href="search.php?class_id=10">Class 10</a></li>
                    <li><a href="search.php?class_id=11">Class 11</a></li>
                    <li><a href="search.php?class_id=12">Class 12</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Resources</h4>
                <ul>
                    <li><a href="resources.php?type=videos">Video Lectures</a></li>
                    <li><a href="resources.php?type=books">PDF Books</a></li>
                    <li><a href="resources.php?type=papers">Past Papers</a></li>
                    <li><a href="resources.php?type=notes">Notes & PPTs</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Boards</h4>
                <ul>
                    <li><a href="search.php?board_id=2">Sindh Board</a></li>
                    <li><a href="search.php?board_id=1">Federal Board</a></li>
                    <li><a href="search.php?board_id=3">Punjab Board</a></li>
                    <li><a href="search.php?board_id=4">KPK Board</a></li>
                    <li><a href="search.php?board_id=5">Balochistan Board</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Learnify. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>