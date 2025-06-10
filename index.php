<!DOCTYPE html>
<html lang="zh-CN">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>阅后即焚</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSEqGa+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css" integrity="sha384-n8MVd4RsNIU0tAv4ct0nTaAbDJwPJzDEaqSD1odI+WdtXRGWt2kTvGFasHpSy3SV" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js" integrity="sha384-XjKyOOlGwcjNTAIQHIpgOno0Hl1YQqzUOEleOLALmuqehneUG+vnGctmUb0ZY0l8" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js" integrity="sha384-+VBxd3r6XgURycqtZ117nYw44OOcIax56Z4dCRWbxyPt0Koah1uHoK0o4+/RRE05" crossorigin="anonymous"></script>
<?php
$envFile = parse_ini_file(__DIR__ . '/.env');
$encryptionKeyFromEnv = $envFile['ENCRYPTION_KEY'];
$siteIcon = $envFile['SITE_ICON'];
$siteDomain = $envFile['SITE_DOMAIN'];
$messageExpiry = isset($envFile['MESSAGE_EXPIRY']) ? $envFile['MESSAGE_EXPIRY'] : '7:0:0:0';

list($days, $hours, $minutes, $seconds) = array_pad(explode(':', $messageExpiry), 4, 0);
$expirySeconds = ($days * 24 * 60 * 60) + ($hours * 60 * 60) + ($minutes * 60) + $seconds;

if (!empty($siteIcon)) {
  if (str_starts_with($siteIcon, 'data:image')) {
    echo '<link rel="icon" href="' . htmlspecialchars($siteIcon) . '">';
  } else {
    echo '<link rel="icon" href="' . htmlspecialchars($siteIcon) . '" type="image/x-icon">';
  }
}
?>
<style>
.message-box {
  border: 1px solid #e2e8f0;
  padding: 20px;
  border-radius: 15px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  background-color: #ffffff;
  width: calc(100% - 2rem);
  max-width: 800px;
  margin: 1rem auto;
  box-sizing: border-box;
}

.sender-info {
  font-size: 1rem;
  color: #4B5563;
  margin-bottom: 1.5rem;
  padding: 0.75rem 1rem;
  background: #F3F4F6;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.sender-info i {
  color: #6B7280;
}

.content-box-wrapper {
  background: #F9FAFB;
  border-radius: 12px;
  padding: 1rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.options-container {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1rem;
  padding: 0.5rem;
  background: #FFFFFF;
  border-radius: 8px;
  border: 1px solid #E5E7EB;
}

.option-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.25rem 0.5rem;
  background: #F3F4F6;
  border-radius: 6px;
  cursor: pointer;
}

.option-item:hover {
  background: #E5E7EB;
}

.option-item input[type="checkbox"] {
  cursor: pointer;
}

.option-item label {
  cursor: pointer;
  user-select: none;
  font-size: 0.875rem;
  color: #4B5563;
}

.content-box {
  background: #FFFFFF;
  border: 1px solid #E5E7EB;
  border-radius: 8px;
  padding: 1rem;
  font-size: 0.875rem;
  line-height: 1.6;
  overflow-x: auto;
  height: 500px;
  overflow-y: auto;
  transition: height 0.3s ease;
}

.content-box #displayContent {
  white-space: pre-wrap;
  word-wrap: break-word;
  word-break: break-word;
}

.content-box.markdown-mode #displayContent {
  white-space: normal;
}

.content-box.auto-height {
  height: auto;
  max-height: none;
}

.content-box pre {
  background: #F3F4F6;
  padding: 1rem;
  border-radius: 6px;
  overflow-x: auto;
}

.content-box code {
  background: #F3F4F6;
  padding: 0.2rem 0.4rem;
  border-radius: 4px;
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
}

.content-box img {
  max-width: 100%;
  height: auto;
  border-radius: 4px;
}

.content-box blockquote {
  border-left: 4px solid #E5E7EB;
  padding-left: 1rem;
  margin: 1rem 0;
  color: #4B5563;
}

.content-box table {
  border-collapse: collapse;
  width: 100%;
  margin: 1rem 0;
}

.content-box th,
.content-box td {
  border: 1px solid #E5E7EB;
  padding: 0.5rem;
  text-align: left;
}

.content-box th {
  background: #F9FAFB;
}


.copy-button {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  width: 100%;
  padding: 0.75rem 1rem;
  background-color: #3B82F6;
  color: white;
  border: none;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.copy-button:hover {
  background-color: #2563EB;
}

.copy-button.copied {
  background-color: #059669;
}

.copy-button.copied .fa-copy {
  display: none;
}

.message-success-container .copy-button.copied span::before {
  content: '已复制';
}

.message-success-container .copy-button span::before {
  content: '复制链接';
}

.content-box-wrapper .copy-button.copied span::before {
  content: '已复制';
}

.content-box-wrapper .copy-button span::before {
  content: '复制内容';
}

.copy-feedback {
  position: fixed;
  top: 1rem;
  left: 50%;
  transform: translateX(-50%) translateY(-1rem);
  background: #3B82F6;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  font-weight: 500;
  opacity: 0;
  transition: opacity 0.3s, visibility 0.3s;
  z-index: 50;
  pointer-events: none;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.copy-feedback.show {
  opacity: 1;
  transform: translateX(-50%) translateY(0);
}

.message-link {
  color: #2563eb;
  font-weight: bold;
  text-decoration: underline;
  word-break: break-all;
}

.message-link-container {
  background: linear-gradient(135deg, #EFF6FF 0%, #E0F2FE 100%);
  padding: 2rem;
  border-radius: 1rem;
  margin: 2rem auto;
  border: 1px solid rgba(96, 165, 250, 0.3);
  text-align: center;
  max-width: 800px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  position: relative;
  overflow: hidden;
}

.message-link-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #60A5FA, #3B82F6);
  border-radius: 4px 4px 0 0;
}

.message-link-container h2 {
  color: #1E40AF;
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
}

.message-link-container .link-wrapper {
  background: white;
  border: 1px solid #E5E7EB;
  border-radius: 0.75rem;
  padding: 1rem;
  margin: 1rem 0;
  position: relative;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.message-link-container #messageLink {
  color: #2563EB;
  font-size: 1rem;
  font-family: ui-monospace, monospace;
  word-break: break-all;
  flex: 1;
  text-align: left;
  padding: 0.5rem;
  background: #F8FAFC;
  border-radius: 0.5rem;
  border: 1px solid #E5E7EB;
  cursor: text;
  user-select: all;
  transition: all 0.2s ease;
}

.message-link-container #messageLink:hover {
  background: #F1F5F9;
}

.copy-actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 1rem;
  justify-content: center;
}

.copy-button {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background-color: #3B82F6;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 0.75rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
  font-size: 0.875rem;
}

.copy-button:hover {
  background-color: #2563eb;
}

.copy-button.copied {
  background-color: #059669;
}

.copy-button i {
  font-size: 1rem;
}


.message-success-container {
  max-width: 600px;
  margin: 2rem auto;
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.success-header {
  background: #60A5FA;
  padding: 1.5rem;
  text-align: center;
  color: white;
}

.success-header h2 {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.success-header p {
  font-size: 0.875rem;
  opacity: 0.9;
  margin: 0;
}

.success-content {
  padding: 1.5rem;
}

.link-display {
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 0.5rem;
  padding: 0.875rem 1rem;
  color: #475569;
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
  font-size: 0.875rem;
  line-height: 1.5;
  word-break: break-all;
  margin-bottom: 1rem;
  text-align: left;
  overflow-x: auto;
  white-space: nowrap;
}


.success-header {
  background: #3B82F6;
  color: white;
  padding: 1.5rem;
  text-align: center;
  border-radius: 0.75rem 0.75rem 0 0;
}

.success-header h2 {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.success-header p {
  font-size: 0.875rem;
  opacity: 0.9;
}

.success-content {
  padding: 1.5rem;
}

.share-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  padding: 0.875rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  font-size: 0.875rem;
  gap: 0.5rem;
  transition: all 0.15s ease;
  border: none;
  cursor: pointer;
  color: white;
  background: #60A5FA;
}

.share-button:hover {
  background: #3B82F6;
}

.share-button:active {
  transform: translateY(1px);
}

.share-button i {
  font-size: 0.875rem;
}


.options-container {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 10px;
}

@media (max-width: 640px) {
  .options-container {
    flex-direction: column;
    gap: 15px;
  }
  
  .options-container button {
    width: 100%;
  }
}


.tab-active {
  border-bottom: 2px solid #3b82f6;
  color: #3b82f6;
  background-color: #f8fafc;
}

.editor-container {
  border: 1px solid #e5e7eb;
  border-radius: 0.375rem;
  background-color: white;
  overflow: hidden;
}

.editor-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem;
  border-bottom: 1px solid #e5e7eb;
  background-color: #f8fafc;
}

.editor-tabs {
  display: flex;
  gap: 1px;
}

.editor-tab {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  cursor: pointer;
  border: none;
  background: none;
}

.editor-options {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.content-area {
  padding: 1rem;
}

.editor-content {
  width: 100%;
  height: 200px;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  padding: 0.5rem;
  background-color: white;
  overflow-y: auto;
  position: relative;
}

#message {
width: 100%;
  height: 100%;
  resize: none;
  outline: none;
  border: none;
  padding: 0;
  display: block;
}

.auto-height .editor-container {
  overflow: visible;
}

.auto-height .editor-content {
  height: auto;
  min-height: 200px;
  overflow: visible;
  max-height: none;
}

.auto-height #message {
  height: 100% !important;
}

.markdown {
  line-height: 1.6;
}

.form-label {
  display: block;
  font-size: 1.1rem;
  font-weight: bold;
  color: #2d3748;
  margin-bottom: 0.5rem;
}

.form-label .optional,
.form-label .markdown-support,
.form-label .time-limit {
  font-size: 0.875rem;
  font-weight: normal;
  color: #6B7280;
  margin-left: 0.5rem;
}


.confirmation-container {
  max-width: 500px;
  margin: 2rem auto;
  padding: 2rem;
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  text-align: center;
}

.confirmation-icon {
  font-size: 3rem;
  color: #3B82F6;
  margin-bottom: 1.5rem;
}

.confirmation-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1F2937;
  margin-bottom: 1rem;
}

.confirmation-text {
  color: #4B5563;
  margin-bottom: 1.5rem;
  line-height: 1.6;
}

.confirmation-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
}

.confirm-button {
  padding: 0.75rem 1.5rem;
  background: #3B82F6;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  min-width: 120px;
}

.confirm-button:hover {
  background: #2563EB;
}

.back-button {
  padding: 0.75rem 1.5rem;
  background: #F3F4F6;
  color: #4B5563;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  min-width: 120px;
}

.back-button:hover {
  background: #E5E7EB;
}

@media (max-width: 640px) {
  .confirmation-container {
    margin: 1rem;
    padding: 1.5rem;
  }
  
  .confirmation-buttons {
    flex-direction: column;
  }
  
  .confirm-button,
  .back-button {
    width: 100%;
  }
}


.content-box.markdown-mode #displayContent h1,
.preview-content h1 { font-size: 32px; }

.content-box.markdown-mode #displayContent h2,
.preview-content h2 { font-size: 24px; }

.content-box.markdown-mode #displayContent h3,
.preview-content h3 { font-size: 20px; }

.content-box.markdown-mode #displayContent h4,
.preview-content h4 { font-size: 16px; }

.content-box.markdown-mode #displayContent h5,
.preview-content h5 { font-size: 14px; }

.content-box.markdown-mode #displayContent h6,
.preview-content h6 { font-size: 12px; }

.content-box.markdown-mode #displayContent h1,
.content-box.markdown-mode #displayContent h2,
.content-box.markdown-mode #displayContent h3,
.content-box.markdown-mode #displayContent h4,
.content-box.markdown-mode #displayContent h5,
.content-box.markdown-mode #displayContent h6,
.preview-content h1,
.preview-content h2,
.preview-content h3,
.preview-content h4,
.preview-content h5,
.preview-content h6 {
  font-weight: bold;
  margin: 16px 0 8px 0;
  line-height: 1.4;
}

.preview-content {
  padding: 16px;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  min-height: 100px;
}
</style>
</head>

<body class="bg-gradient-to-r from-blue-100 to-blue-300 font-sans min-h-screen flex items-center justify-center">

<div class="container mx-auto p-8">

<?php
define('ENCRYPTION_KEY_LEN', 32);

function generateEncryptionKey()
{
  return bin2hex(random_bytes(ENCRYPTION_KEY_LEN / 2));
}

function encrypt($plaintext, $key)
{
  global $encryptionKeyFromEnv;
  $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
  
  $intermediateKey = hash_hmac('sha256', $key, $encryptionKeyFromEnv, true);
  $encrypted = openssl_encrypt($plaintext, 'aes-256-cbc', $intermediateKey, 0, $iv);
  
  return base64_encode($iv . $encrypted);
}

function decrypt($ciphertext, $key)
{
  global $encryptionKeyFromEnv;
  $ciphertext = base64_decode($ciphertext);
  $iv = substr($ciphertext, 0, openssl_cipher_iv_length('aes-256-cbc'));
  $encrypted = substr($ciphertext, openssl_cipher_iv_length('aes-256-cbc'));
  
  $intermediateKey = hash_hmac('sha256', $key, $encryptionKeyFromEnv, true);
  $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $intermediateKey, 0, $iv);
  
  return $decrypted;
}

function generateRandomFilename($length = 16)
{
  return 'messages/' . substr(bin2hex(random_bytes($length / 2)), 0, $length) . '.json';
}

function generateVerificationCode($length = 8)
{
  return bin2hex(random_bytes($length / 2));
}

function hashVerificationCode($code)
{
  return hash('sha256', $code);
}

function hashPassword($password)
{
  return $password ? hash('sha256', $password) : null;
}

function sanitizeInput($data)
{
  return htmlspecialchars(trim($data));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['confirm'])) {
  $senderName = isset($_POST['senderName']) ? sanitizeInput($_POST['senderName']) : '';
  $senderNote = isset($_POST['senderNote']) ? sanitizeInput($_POST['senderNote']) : '';
  $senderPassword = isset($_POST['senderPassword']) ? sanitizeInput($_POST['senderPassword']) : '';
  
  $userDays = isset($_POST['expiry_days']) ? intval($_POST['expiry_days']) : 0;
  $userHours = isset($_POST['expiry_hours']) ? intval($_POST['expiry_hours']) : 0;
  $userMinutes = isset($_POST['expiry_minutes']) ? intval($_POST['expiry_minutes']) : 0;
  $userSeconds = isset($_POST['expiry_seconds']) ? intval($_POST['expiry_seconds']) : 0;
  
  $userExpirySeconds = ($userDays * 24 * 60 * 60) + ($userHours * 60 * 60) + ($userMinutes * 60) + $userSeconds;
  
  if ($userExpirySeconds > $expirySeconds) {
    echo '<div class="message-box mb-6">';
    echo '<p class="text-red-500">设置的过期时间超过系统允许的最大值（' . $messageExpiry . '）。</p>';
    echo '</div>';
    exit;
  }
  
  if (!empty($_POST['message'])) {
    $content = $_POST['message'];
    
    $verificationCode = generateVerificationCode();
    $hashedVerificationCode = hashVerificationCode($verificationCode);
    $hashedSenderPassword = hashPassword($senderPassword);
    
    $randomKey = generateEncryptionKey();
    $encryptedMessage = encrypt($content, $randomKey);
    $encryptedSenderName = encrypt($senderName, $randomKey);
    $encryptedSenderNote = encrypt($senderNote, $randomKey);
    
    $keyEncryptedWithVerificationCode = encrypt($randomKey, $hashedVerificationCode);
    $keyEncryptedWithSenderPassword = $hashedSenderPassword ? encrypt($randomKey, $hashedSenderPassword) : null;
    
    $filename = generateRandomFilename();
    
    if (!file_exists('messages')) mkdir('messages', 0755, true);
    
    $messageData = [
      'senderNameEncrypted' => $encryptedSenderName,
      'senderNoteEncrypted' => $encryptedSenderNote,
      'senderPasswordHash' => $hashedSenderPassword,
      'messageEncrypted' => $encryptedMessage,
      'keyEncryptedWithVerificationCode' => $keyEncryptedWithVerificationCode,
      'keyEncryptedWithSenderPassword' => $keyEncryptedWithSenderPassword,
      'hashedVerificationCode' => $hashedVerificationCode,
      'createdAt' => time(),
      'expirySeconds' => $userExpirySeconds > 0 ? $userExpirySeconds : $expirySeconds
      ];
    
    file_put_contents($filename, json_encode($messageData), LOCK_EX);
    
    $messageLink = "?file=" . urlencode(basename($filename)) . "&code=" . urlencode($verificationCode);
    
    echo '<div class="message-success-container">
    <div class="success-header">
    <h2>链接生成成功</h2>
    <p>此链接仅可查看一次，阅读后将自动销毁</p>
    </div>
    
    <div class="success-content">
    <div class="link-display select-none">
    ' . htmlspecialchars($siteDomain) . '/' . htmlspecialchars($messageLink) . '
    </div>
    
    <button onclick="copyToClipboard(\'' . htmlspecialchars($siteDomain) . '/' . htmlspecialchars($messageLink) . '\')" 
            class="copy-button">
      <i class="fas fa-copy"></i>
      <span></span>
    </button>
    </div>
    </div>
    <div id="copyFeedback" class="copy-feedback">
    <i class="fas fa-check mr-1"></i>复制成功
    </div>

    <script>
    function copyToClipboard(text) {
      navigator.clipboard.writeText(text).then(() => {
        const feedback = document.getElementById("copyFeedback");
        feedback.classList.add("show");
        setTimeout(() => {
          feedback.classList.remove("show");
        }, 2000);
      }).catch(err => {
        console.error("Failed to copy:", err);
      });
    }
    </script>';
  } else {
    echo '<div class="message-box mb-6">';
    echo '<p class="text-red-500">消息不能为空。</p>';
    echo '</div>';
    exit;
  }
} else if (isset($_GET['file']) && isset($_GET['code'])) {
  $filename = basename($_GET['file']);
  $verificationCode = sanitizeInput($_GET['code']);
  
  if (file_exists("messages/$filename")) {
    $messageData = json_decode(file_get_contents("messages/$filename"), true);
    
    $createdAt = isset($messageData['createdAt']) ? $messageData['createdAt'] : 0;
    $currentTime = time();
    $messageExpirySeconds = isset($messageData['expirySeconds']) ? $messageData['expirySeconds'] : $expirySeconds;
    
    if ($currentTime - $createdAt > $messageExpirySeconds) {
      unlink("messages/$filename");
      echo '<div class="message-box mb-6">';
      echo '<p class="text-red-500">消息已过期。</p>';
      echo '</div>';
      exit;
    }
    
    if (isset($messageData['hashedVerificationCode']) && hashVerificationCode($verificationCode) === $messageData['hashedVerificationCode']) {
      if (isset($_GET['confirm'])) {
        $enteredSenderPassword = isset($_POST['senderPassword']) ? sanitizeInput($_POST['senderPassword']) : '';
        $hashedEnteredSenderPassword = hashPassword($enteredSenderPassword);
        
        if (is_null($messageData['senderPasswordHash']) || (!empty($enteredSenderPassword) && $hashedEnteredSenderPassword === $messageData['senderPasswordHash'])) {
          unlink("messages/$filename");
          
          if (is_null($messageData['senderPasswordHash'])) {
            $randomKey = decrypt($messageData['keyEncryptedWithVerificationCode'], $messageData['hashedVerificationCode']);
          } else {
            $randomKey = decrypt($messageData['keyEncryptedWithSenderPassword'], $hashedEnteredSenderPassword);
          }
          
          
          $decryptedMessage = decrypt($messageData['messageEncrypted'], $randomKey);
          $decryptedSenderName = decrypt($messageData['senderNameEncrypted'], $randomKey);
          $decryptedSenderNote = decrypt($messageData['senderNoteEncrypted'], $randomKey);
          
          echo '<div class="message-box mb-6 viewing-message">';
          
          if (!empty($decryptedSenderName) || !empty($decryptedSenderNote)) { ?>
            <div class="sender-info">
              <i class="fas fa-user"></i>
              <span>
                <?php echo empty($decryptedSenderName) ? '无发件人' : htmlspecialchars($decryptedSenderName); ?>
                (<?php echo empty($decryptedSenderNote) ? '无备注' : htmlspecialchars($decryptedSenderNote); ?>)
              </span>
            </div>
          <?php } else { ?>
            <div class="sender-info">
              <i class="fas fa-user"></i>
              <span>无发件人（无备注）</span>
            </div>
          <?php } ?>
          
          <div class="content-box-wrapper">
            <div class="options-container mb-4">
              <label class="option-item" id="markdownLabel">
                <input type="checkbox" id="markdownCheckbox" onchange="toggleMarkdown(this)">
                <span>渲染 Markdown</span>
              </label>
              <label class="option-item" id="heightLabel">
                <input type="checkbox" id="autoHeightCheckbox" onchange="toggleHeight(this)">
                <span>自适应高度</span>
              </label>
            </div>
            
            <div class="content-box mb-6" id="message-text">
              <div id="rawContent" style="display:none"><?php echo htmlspecialchars($decryptedMessage); ?></div>
              <div id="displayContent"><?php echo htmlspecialchars($decryptedMessage); ?></div>
            </div>

            <button class="copy-button" onclick="copyMessage()">
              <i class="fas fa-copy"></i>
              <span></span>
            </button>
          </div>
          
          <script>
          function toggleMarkdown(checkbox) {
            const messageText = document.getElementById('message-text');
            const rawContent = document.getElementById('rawContent');
            const displayContent = document.getElementById('displayContent');
            const markdownLabel = document.getElementById('markdownLabel');
            
            if (checkbox.checked) {
              markdownLabel.classList.add('active');
              messageText.classList.add('markdown-mode');
              const renderer = new marked.Renderer();
              marked.setOptions({
                renderer: renderer,
                breaks: true,
                gfm: true,
                headerIds: false
              });
              
              displayContent.innerHTML = marked.parse(rawContent.textContent);
              renderMathInElement(displayContent, {
                delimiters: [
                  {left: "$$", right: "$$", display: true},
                  {left: "$", right: "$", display: false},
                  {left: "\\[", right: "\\]", display: true},
                  {left: "\\(", right: "\\)", display: false}
                ],
                throwOnError: false,
                output: "html"
              });
            } else {
              markdownLabel.classList.remove('active');
              messageText.classList.remove('markdown-mode');
              displayContent.textContent = rawContent.textContent;
            }
          }
          
          function toggleHeight(checkbox) {
            const messageText = document.getElementById('message-text');
            const heightLabel = document.getElementById('heightLabel');
            
            if (checkbox.checked) {
              heightLabel.classList.add('active');
              messageText.classList.add('auto-height');
            } else {
              heightLabel.classList.remove('active');
              messageText.classList.remove('auto-height');
              messageText.scrollTop = 0;
            }
          }
          
          document.addEventListener('DOMContentLoaded', function() {
            const rawContent = document.getElementById('rawContent');
            const displayContent = document.getElementById('displayContent');
            rawContent.textContent = displayContent.textContent;
          });
          
          function copyMessage() {
            const messageText = document.getElementById('message-text');
            const text = messageText.innerText;
            
            navigator.clipboard.writeText(text).then(() => {
              const copyButton = document.querySelector('.copy-button');
              const originalContent = copyButton.innerHTML;
              
              copyButton.innerHTML = '<i class="fas fa-check"></i><span>已复制</span>';
              copyButton.style.background = '#059669';
              
              setTimeout(() => {
                copyButton.innerHTML = originalContent;
                copyButton.style.background = '';
              }, 2000);
            }).catch(err => {
              console.error('复制失败:', err);
              const copyButton = document.querySelector('.copy-button');
              const originalContent = copyButton.innerHTML;
              
              copyButton.innerHTML = '<i class="fas fa-times"></i><span>复制失败</span>';
              copyButton.style.background = '#DC2626';
              
              setTimeout(() => {
                copyButton.innerHTML = originalContent;
                copyButton.style.background = '';
              }, 2000);
            });
          }
          </script>
          <?php
          echo '</div>';
        } else {
          echo '<div class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto text-center">';
          echo '<h2 class="text-xl font-semibold mb-3">请输入密码</h2>';
          echo '<p class="text-gray-600 text-sm mb-4">此消息受密码保护，查看后将被永久删除。</p>';
          $errorMsg = '';
          if (isset($_POST['senderPassword'])) {
            if (!isset($messageData['password']) || !password_verify($_POST['senderPassword'], $messageData['password'])) {
              $errorMsg = '密码错误，请重试';
            }
          }
          ?>
          <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded-lg mb-4 text-sm" style="<?php echo empty($errorMsg) ? 'display: none;' : ''; ?>">
            <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($errorMsg); ?>
          </div>
          <form method="POST" action="?file=<?php echo urlencode($_GET['file']); ?>&code=<?php echo urlencode($_GET['code']); ?>&confirm=1">
            <div class="relative mb-4">
              <input type="password" 
                     id="password" 
                     name="senderPassword" 
                     class="w-full px-4 py-2 border <?php echo !empty($errorMsg) ? 'border-red-400' : 'border-gray-300'; ?> rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                     placeholder="请输入密码"
                     required
                     autofocus>
              <button type="button" 
                      onclick="togglePassword()"
                      class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                <i class="fas fa-eye"></i>
              </button>
            </div>

            <div class="flex gap-3">
              <button type="submit"
                      class="flex-1 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                确认
              </button>
              <button type="button"
                      onclick="window.history.back()" 
                      class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200">
                返回
              </button>
            </div>
          </form>
          <script>
          function togglePassword() {
            const input = document.getElementById("password");
            const icon = document.querySelector(".fa-eye");
            input.type = input.type === "password" ? "text" : "password";
            icon.classList.toggle("fa-eye-slash");
            icon.classList.toggle("fa-eye");
          }
          </script>
          <?php
          echo '</div>';
        }
      } else {
        if (!is_null($messageData['senderPasswordHash'])) {
          echo '<div class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto text-center">';
          echo '<h2 class="text-xl font-semibold mb-3">请输入密码</h2>';
          echo '<p class="text-gray-600 text-sm mb-4">此消息受密码保护，查看后将被永久删除。</p>';
          $errorMsg = '';
          if (isset($_POST['senderPassword'])) {
            if (!isset($messageData['password']) || !password_verify($_POST['senderPassword'], $messageData['password'])) {
              $errorMsg = '密码错误，请重试';
            }
          }
          ?>
          <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded-lg mb-4 text-sm" style="<?php echo empty($errorMsg) ? 'display: none;' : ''; ?>">
            <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($errorMsg); ?>
          </div>
          <form method="POST" action="?file=<?php echo urlencode($_GET['file']); ?>&code=<?php echo urlencode($_GET['code']); ?>&confirm=1">
            <div class="relative mb-4">
              <input type="password" 
                     id="password" 
                     name="senderPassword" 
                     class="w-full px-4 py-2 border <?php echo !empty($errorMsg) ? 'border-red-400' : 'border-gray-300'; ?> rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                     placeholder="请输入密码"
                     required
                     autofocus>
              <button type="button" 
                      onclick="togglePassword()"
                      class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                <i class="fas fa-eye"></i>
              </button>
            </div>

            <div class="flex gap-3">
              <button type="submit"
                      class="flex-1 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                确认
              </button>
              <button type="button"
                      onclick="window.history.back()" 
                      class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200">
                返回
              </button>
            </div>
          </form>
          <script>
          function togglePassword() {
            const input = document.getElementById("password");
            const icon = document.querySelector(".fa-eye");
            input.type = input.type === "password" ? "text" : "password";
            icon.classList.toggle("fa-eye-slash");
            icon.classList.toggle("fa-eye");
          }
          </script>
          <?php
          echo '</div>';
        } else {
          echo '<div class="confirmation-container">';
          echo '<div class="confirmation-icon">';
          echo '<i class="fas fa-envelope-open-text"></i>';
          echo '</div>';
          echo '<h2 class="confirmation-title">确认查看消息？</h2>';
          echo '<p class="confirmation-text">此消息只能查看一次，查看后将被永久删除。请确认是否现在查看？</p>';
          echo '<div class="confirmation-buttons">';
          echo '<form method="POST" action="?file=' . urlencode($_GET['file']) . '&code=' . urlencode($_GET['code']) . '&confirm=1" style="margin: 0;">';
          echo '<button type="submit" class="confirm-button">';
          echo '<i class="fas fa-eye"></i> 确认查看';
          echo '</button>';
          echo '</form>';
          echo '<a href="/" class="back-button">';
          echo '<i class="fas fa-arrow-left"></i> 返回';
          echo '</a>';
          echo '</div>';
          echo '</div>';
        }
      }
    } else {
      echo '<div class="message-box mb-6">';
      echo '<p class="text-red-500">验证码错误或链接已失效。</p>';
      echo '</div>';
    }
  } else {
    echo '<div class="message-box mb-6">';
    echo '<p class="text-red-500">链接已失效。</p>';
    echo '</div>';
  }
} else { ?>
  
  <div class="message-box mb-6">
  <h2 class="text-2xl font-bold mb-4 text-center">发送阅后即焚消息</h2>
  <form method="POST">
  <div class="mb-4">
  <label for="senderName" class="form-label">
    您的名字
    <span class="text-sm text-gray-500 ml-2">可选</span>
    </label>
    <input type="text" id="senderName" name="senderName"
    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
    placeholder="请输入您的名字">
    </div>
    <div class="mb-4">
    <label for="senderNote" class="form-label">
      备注
      <span class="text-sm text-gray-500 ml-2">可选</span>
      </label>
      <input type="text" id="senderNote" name="senderNote"
      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
      placeholder="请输入备注">
      </div>
      <div class="mb-4">
      <label for="senderPassword" class="form-label">
        设置密码
        <span class="text-sm text-gray-500 ml-2">可选</span>
        </label>
        <input type="password" id="senderPassword" name="senderPassword"
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        placeholder="请输入密码">
        </div>
        <div class="mb-4">
        <label for="message" class="form-label">
          消息内容
          <span class="text-sm text-gray-500 ml-2">支持 Markdown 格式</span>
          </label>
          <div class="editor-container shadow">
          <div class="editor-toolbar">
          <div class="editor-tabs">
          <button type="button" id="editTab"
          class="editor-tab tab-active"
          onclick="switchTab('edit')">编辑</button>
          <button type="button" id="previewTab"
          class="editor-tab"
          onclick="switchTab('preview')">预览</button>
          </div>
          <div class="editor-options">
          <label class="flex items-center">
          <input type="checkbox" id="autoHeight" class="mr-2" onchange="toggleAutoHeight()">
          <span class="text-sm text-gray-500 ml-2">自适应高度</span>
          </label>
          </div>
          </div>
          <div id="editArea" class="content-area">
          <div class="editor-content">
          <textarea id="message" name="message" required
          oninput="updatePreview(); autoResize();"></textarea>
          </div>
          </div>
          <div id="previewArea" class="content-area hidden">
          <div class="editor-content markdown preview-content"></div>
          </div>
          </div>
          </div>
          <div class="mb-4">
          <label for="expiry" class="form-label">
            过期时间
            <span class="text-sm text-gray-500 ml-2">最大允许时间 <?php echo $messageExpiry; ?></span>
  <span id="currentExpiry" class="text-sm ml-2">当前设置：0天0时0分0秒</span>
  </label>
  <div class="flex space-x-2">
  <div class="flex-1">
  <div class="relative">
  <input type="number" id="days" name="expiry_days" min="0" max="365" value="0"
  class="shadow appearance-none border rounded w-full py-2 pl-3 pr-12 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
  oninput="validateAndUpdateTime()">
  <span class="absolute right-3 top-2 text-gray-600 text-sm">天</span>
  </div>
  </div>
  <div class="flex-1">
  <div class="relative">
  <input type="number" id="hours" name="expiry_hours" min="0" max="23" value="0"
  class="shadow appearance-none border rounded w-full py-2 pl-3 pr-12 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
  oninput="validateAndUpdateTime()">
  <span class="absolute right-3 top-2 text-gray-600 text-sm">时</span>
  </div>
  </div>
  <div class="flex-1">
  <div class="relative">
  <input type="number" id="minutes" name="expiry_minutes" min="0" max="59" value="0"
  class="shadow appearance-none border rounded w-full py-2 pl-3 pr-12 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
  oninput="validateAndUpdateTime()">
  <span class="absolute right-3 top-2 text-gray-600 text-sm">分</span>
  </div>
  </div>
  <div class="flex-1">
  <div class="relative">
  <input type="number" id="seconds" name="expiry_seconds" min="0" max="59" value="0"
  class="shadow appearance-none border rounded w-full py-2 pl-3 pr-12 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
  oninput="validateAndUpdateTime()">
  <span class="absolute right-3 top-2 text-gray-600 text-sm">秒</span>
  </div>
  </div>
  </div>
  </div>
  <button type="submit"
  class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">发送</button>
  </form>
  </div>
  
  <?php } ?>

<div id="copyFeedback" class="copy-feedback"></div>

<script>
function copyToClipboard(text) {
  if (!text) return;
  
  navigator.clipboard.writeText(text).then(() => {
    const button = document.querySelector('.copy-button');
    button.classList.add('copied');
    setTimeout(() => {
      button.classList.remove('copied');
    }, 1000);
  }).catch(err => {
    console.error('Failed to copy:', err);
  });
}
</script>

<script>
const maxTimeStr = '<?php echo $messageExpiry; ?>';
const [maxDays, maxHours, maxMinutes, maxSeconds] = maxTimeStr.split(':').map(Number);
const maxTotalSeconds = (maxDays * 24 * 60 * 60) + (maxHours * 60 * 60) + (maxMinutes * 60) + maxSeconds;

function formatTime(days, hours, minutes, seconds) {
  let timeText = [];
  if (days > 0) timeText.push(days + '天');
  if (hours > 0) timeText.push(hours + '小时');
  if (minutes > 0) timeText.push(minutes + '分钟');
  if (seconds > 0) timeText.push(seconds + '秒');
  return timeText.length > 0 ? timeText.join(' ') : '0秒';
}

document.addEventListener('DOMContentLoaded', function() {
  const lastTime = localStorage.getItem('lastExpiryTime');
  if (lastTime) {
    try {
      const timeValues = JSON.parse(lastTime);
      document.getElementById('days').value = timeValues.days || 0;
      document.getElementById('hours').value = timeValues.hours || 0;
      document.getElementById('minutes').value = timeValues.minutes || 0;
      document.getElementById('seconds').value = timeValues.seconds || 0;
    } catch (e) {
      console.error('Error restoring last expiry time:', e);
    }
  }
  validateAndUpdateTime();
});

function validateAndUpdateTime() {
  const days = parseInt(document.getElementById('days').value) || 0;
  const hours = parseInt(document.getElementById('hours').value) || 0;
  const minutes = parseInt(document.getElementById('minutes').value) || 0;
  const seconds = parseInt(document.getElementById('seconds').value) || 0;
  
  const maxTimeStr = '<?php echo $messageExpiry; ?>';
  const [maxDays, maxHours, maxMinutes, maxSeconds] = maxTimeStr.split(':').map(Number);
  
  const currentTotalSeconds = days * 86400 + hours * 3600 + minutes * 60 + seconds;
  const maxTotalSeconds = maxDays * 86400 + maxHours * 3600 + maxMinutes * 60 + maxSeconds;
  
  localStorage.setItem('lastExpiryTime', JSON.stringify({
    days, hours, minutes, seconds
  }));
  
  const currentExpiry = document.getElementById('currentExpiry');
  currentExpiry.textContent = `当前设置：${formatTime(days, hours, minutes, seconds)}`;
  
  if (currentTotalSeconds > maxTotalSeconds) {
    currentExpiry.classList.add('text-red-500');
    currentExpiry.classList.remove('text-gray-500');
  } else {
    currentExpiry.classList.remove('text-red-500');
    currentExpiry.classList.add('text-gray-500');
  }
}

function autoResize() {
  const message = document.getElementById('message');
  const editorContent = message.closest('.editor-content');
  const previewContent = document.getElementById('previewArea').querySelector('.editor-content');
  
  if (document.getElementById('autoHeight').checked) {
    const newHeight = Math.max(200, message.scrollHeight);
    editorContent.style.height = newHeight + 'px';
    previewContent.style.height = 'auto';
    const previewHeight = Math.max(newHeight, previewContent.scrollHeight);
    previewContent.style.height = previewHeight + 'px';
  }
}

function toggleAutoHeight() {
  const isAutoHeight = document.getElementById('autoHeight').checked;
  const editArea = document.getElementById('editArea');
  const previewArea = document.getElementById('previewArea');
  
  if (isAutoHeight) {
    editArea.classList.add('auto-height');
    previewArea.classList.add('auto-height');
    autoResize();
  } else {
    editArea.classList.remove('auto-height');
    previewArea.classList.remove('auto-height');
    const editorContent = document.getElementById('editArea').querySelector('.editor-content');
    const previewContent = previewArea.querySelector('.editor-content');
    editorContent.style.height = '200px';
    previewContent.style.height = '200px';
  }
}

function switchTab(tab) {
  const editTab = document.getElementById('editTab');
  const previewTab = document.getElementById('previewTab');
  const editArea = document.getElementById('editArea');
  const previewArea = document.getElementById('previewArea');
  
  if (tab === 'edit') {
    editTab.classList.add('tab-active');
    previewTab.classList.remove('tab-active');
    editArea.classList.remove('hidden');
    previewArea.classList.add('hidden');
  } else {
    editTab.classList.remove('tab-active');
    previewTab.classList.add('tab-active');
    editArea.classList.add('hidden');
    previewArea.classList.remove('hidden');
    updatePreview();
  }
}

function updatePreview() {
  const input = document.getElementById('message');
  const preview = document.getElementById('previewArea').querySelector('.editor-content');
  const renderer = new marked.Renderer();
  
  marked.setOptions({
    renderer: renderer,
    breaks: true,
    gfm: true,
    headerIds: false
  });
  
  preview.innerHTML = marked.parse(input.value);
  
  renderMathInElement(preview, {
    delimiters: [
      {left: "$$", right: "$$", display: true},
      {left: "$", right: "$", display: false},
      {left: "\\\\[", right: "\\\\]", display: true},
      {left: "\\\\(", right: "\\\\)", display: false}
    ],
    throwOnError: false,
    output: "html"
  });
  
  const isAutoHeight = document.getElementById('autoHeight').checked;
  if (!isAutoHeight) {
    preview.style.height = '200px';
    preview.style.overflowY = 'auto';
  }
}

document.getElementById('message').addEventListener('keydown', function(e) {
  if (e.key === 'Tab') {
    e.preventDefault();
    const start = this.selectionStart;
    const end = this.selectionEnd;
    this.value = this.value.substring(0, start) + '    ' + this.value.substring(end);
    this.selectionStart = this.selectionEnd = start + 4;
    updatePreview();
  }
});

document.getElementById('message').addEventListener('input', function() {
  if (document.getElementById('autoHeight').checked) {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
  }
});

document.querySelectorAll('input[type="number"]').forEach(input => {
  input.addEventListener('wheel', function(e) {
    e.preventDefault();
  });
});
</script>

</div>

</body>

</html>
