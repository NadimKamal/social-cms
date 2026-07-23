<?php if (isLoggedIn()) : ?>

    </main>
</div>

<?php else : ?>

    </main>

<?php endif; ?>

<footer class="bg-white border-t">
    <div class="text-center py-6">
        © <?= date('Y') ?> Social CMS
    </div>
</footer>

<script src="<?= APP_URL ?>assets/js/toaster.js"></script>

<?php
$toast = getToast();
if ($toast):
?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    toaster.<?= e($toast['type']) ?>(
        <?= json_encode($toast['message']) ?>
    );
});
</script>
<?php endif; ?>

</body>
</html>