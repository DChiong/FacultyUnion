<?php
// Simple footer
?>
<style>
    /* Clean & Readable Footer Styles */
    .footer-clean-simple {
        background-color: #212529; /* Modern, soft dark background */
        color: #d1d5db; /* Light gray for easy readability */
        padding: 20px 0;
        font-size: 0.85rem;
        line-height: 1.6;
    }

    /* Footer Bottom / Copyright */
    .footer-bottom-simple {
        font-size: 0.85rem;
        color: #9ca3af;
    }
</style>

<footer id="footer" class="footer-clean-simple">
    <div class="container">
        <div class="row footer-bottom-simple text-center">
            <div class="col-12">
                &copy; <span id="footer-year-simple"></span> <strong>WMSU Faculty Union</strong>. All Rights Reserved.
            </div>
        </div>
    </div>
</footer>

<script>
    document.getElementById('footer-year-simple').textContent = new Date().getFullYear();
</script>
