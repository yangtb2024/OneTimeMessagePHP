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
        }

        .message-box.viewing-message {
            margin-top: 50px;
        }

        .message-link {
            color: #2563eb;
            font-weight: bold;
            text-decoration: underline;
            word-break: break-all;
        }

        .message-link:hover {
            color: #1d4ed8;
        }

        .message-link-container {
            background-color: #EFF6FF;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            border: 1px solid #60A5FA;
            text-align: center;
        }

        .message-link-container #messageLink {
            color: #3B82F6;
            font-size: 18px;
            font-weight: 600;
            text-decoration: none;
            word-break: break-all;
        }

        .message-link-container #messageLink:hover {
            color: #2563EB;
        }

        .content-box-wrapper {
            position: relative;
            margin-top: 10px;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 10px;
        }

        .content-box {
            min-height: 100px;
            max-height: 500px;
            overflow-y: auto;
            padding: 8px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 10px;
            white-space: pre-wrap;
            word-wrap: break-word;
            line-height: 1.2;
        }

        .content-box.auto-height {
            max-height: none;
            height: auto !important;
            overflow: visible !important;
        }

        .content-box::-webkit-scrollbar {
            width: 0;
            height: 0;
        }

        .content-box {
            scrollbar-width: thin;
            scrollbar-color: #888 #f1f1f1;
        }

        .content-box::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .content-box::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .content-box::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .copy-button {
            background-color: #3B82F6;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            color: white;
            transition: background-color 0.3s ease, color 0.3s ease;
            right: 14px;
            font-size: 14px;
            z-index: 10;
            position: absolute;
            top: 3px;
        }

        .copy-button.copied {
            background-color: #34D399;
        }

        .copy-button:hover {
            background-color: #2563eb;
        }

        .sender-info {
            background-color: #1D4ED8;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
            font-size: 16px;
            color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .confirmation-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 20px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .confirmation-box h2 {
            font-size: 24px;
            font-weight: bold;
            color: #3B82F6;
            margin-bottom: 20px;
        }

        .confirmation-box p {
            font-size: 18px;
            color: #475569;
            margin-bottom: 20px;
        }

        .confirmation-box form {
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .confirmation-box input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .confirmation-box button {
            background-color: #3B82F6;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
            margin: 10px 0;
            width: 48%;
        }

        .confirmation-box .back-link {
            background-color: #E53E3E;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
            margin: 10px 0;
            width: 48%;
        }

        .confirmation-box button:hover {
            background-color: #2563eb;
        }

        .confirmation-box .back-link:hover {
            background-color: #C53030;
        }

        textarea:focus {
            outline: none;
            resize: none;
        }

        .confirmation-box form>*:not(:first-child) {
            margin-left: 2%;
        }

        textarea,
        input {
            resize: none;
        }

        input[type="text"],
        input[type="password"],
        textarea {
            border: 1px solid #cbd5e0;
            border-radius: 0.375rem;
            padding: 0.75rem;
            transition: border-color 0.2s ease-in-out;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        textarea:focus {
            border-color: #4299e1;
            outline: none;
            box-shadow: 0 0 0 2px rgba(66, 153, 225, 0.2);
        }

        button[type="submit"] {
            background-color: #4299e1;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }

        button[type="submit"]:hover {
            background-color: #3182ce;
        }

        .link-box {
            display: inline-block;
            background-color: #EFF6FF;
            padding: 5px 10px;
            border-radius: 5px;
            margin-left: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            border: 1px solid #60A5FA;
        }

        .link-box:hover {
            background-color: #DBEAFE;
            border-color: #3B82F6;
        }

        .link-box #messageLink {
            color: #3B82F6;
            text-decoration: none;
        }

        .link-box #messageLink:hover {
            color: #2563EB;
        }

        .confirmation-box form {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .confirmation-box button,
        .confirmation-box .back-link {
            width: calc(50% - 1%);
        }

        .message-box.viewing-message {
            margin-top: 50px;
        }

        .message-box button[type="submit"] {
            width: 100%;
        }

        .markdown * {
            margin: 0;
            padding: 0;
        }
        
        .markdown h1, .markdown h2, .markdown h3, .markdown h4, .markdown h5, .markdown h6 {
            margin: 0.3rem 0 0.1rem 0;
            line-height: 1.2;
        }
        
        .markdown h1 {
            font-size: 2rem;
            font-weight: bold;
        }
        
        .markdown h2 {
            font-size: 1.7rem;
            font-weight: bold;
        }
        
        .markdown h3 {
            font-size: 1.4rem;
            font-weight: bold;
        }
        
        .markdown h4 {
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .markdown h5 {
            font-size: 1.1rem;
            font-weight: bold;
        }
        
        .markdown h6 {
            font-size: 1rem;
            font-weight: bold;
        }
        
        .markdown p {
            margin: 0.1rem 0;
            line-height: 1.2;
        }
        
        .markdown ul, .markdown ol {
            margin: 0.1rem 0;
            padding-left: 1.2rem;
        }
        
        .markdown li {
            margin: 0;
            line-height: 1.2;
        }
        
        .markdown br {
            display: none;
        }
        
        .options-container {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 10px;
        }
        
        .option-item {
            display: flex;
            align-items: center;
            white-space: nowrap;
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
            border-radius: 0.25rem;
            padding: 0.5rem;
            background-color: white;
            overflow-y: auto;
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
        
        .form-label .optional,
        .form-label .markdown-support,
        .form-label .time-limit {
            font-size: 0.875rem;
            font-weight: normal;
            color: #6B7280;
            margin-left: 0.5rem;
            text-sm text-gray-500 ml-2;
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

            echo '<div class="message-box mb-6 bg-green-100">';
            echo '<h2 class="text-gray-700 text-center mb-2">您的阅后即焚链接</h2>';
            echo '<div class="message-link-container" onclick="copyLinkToClipboard()">';
            echo '<span id="messageLink">' . htmlspecialchars($siteDomain) . '/' . htmlspecialchars($messageLink) . '</span>';
            echo '</div>';
            echo '</div>';
            echo '<script>
        function copyLinkToClipboard() {
        const messageLink = document.getElementById("messageLink").innerText;
        navigator.clipboard.writeText(messageLink);
        alert("链接已复制到剪贴板！");
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

                        if (empty($decryptedSenderName) && empty($decryptedSenderNote)) {
                            echo '<div class="sender-info">无发件人（无备注）</div>';
                        } else {
                            echo '<div class="sender-info">发件人: ' .
                                (empty($decryptedSenderName) ? '无发件人' : htmlspecialchars($decryptedSenderName)) .
                                ' (' .
                                (empty($decryptedSenderNote) ? '无备注' : htmlspecialchars($decryptedSenderNote)) .
                                ')</div>';
                        }
                        echo '<div class="content-box-wrapper">';

                        echo '<div class="options-container">';
                        echo '<div class="option-item">';
                        echo '<input type="checkbox" id="markdownCheckbox" name="markdownCheckbox">';
                        echo '<label for="markdownCheckbox" class="ml-2">渲染 Markdown</label>';
                        echo '</div>';
                        echo '<div class="option-item">';
                        echo '<input type="checkbox" id="autoHeightCheckbox" name="autoHeightCheckbox">';
                        echo '<label for="autoHeightCheckbox" class="ml-2">自适应高度</label>';
                        echo '</div>';
                        echo '</div>';

                        echo '<div class="content-box markdown' . (isset($_POST['autoHeightCheckbox']) && $_POST['autoHeightCheckbox'] === 'on' ? ' auto-height' : '') . '" id="message-text">' . 
                            (isset($_POST['markdownCheckbox']) && $_POST['markdownCheckbox'] === 'on' ? 
                            '<div id="markdownContent" style="display:none;">' . htmlspecialchars($decryptedMessage) . '</div>' .
                            '<div id="renderedContent"></div>' .
                            '<script>
                                const renderer = new marked.Renderer();
                                marked.setOptions({
                                    renderer: renderer,
                                    breaks: true,
                                    gfm: true
                                });
                                const renderedContent = document.getElementById("renderedContent");
                                const markdownContent = document.getElementById("markdownContent");
                                renderedContent.innerHTML = marked.parse(markdownContent.innerText);
                                renderMathInElement(renderedContent, {
                                    delimiters: [
                                        {left: "$$", right: "$$", display: true},
                                        {left: "$", right: "$", display: false},
                                        {left: "\\\\[", right: "\\\\]", display: true},
                                        {left: "\\\\(", right: "\\\\)", display: false}
                                    ],
                                    throwOnError: false,
                                    output: "html"
                                });
                            </script>' : 
                            nl2br(htmlspecialchars($decryptedMessage))) . 
                        '</div>';
                        echo '<button class="copy-button" onclick="copyToClipboard()"><i class="fas fa-copy"></i> 复制</button>';
                        echo '</div>';

                        echo '<script>
                function copyToClipboard() {
                  const messageText = document.getElementById("message-text");
                  const tempInput = document.createElement("textarea");
                  tempInput.value = messageText.innerText;
                  document.body.appendChild(tempInput);
                  tempInput.select();
                  document.execCommand("copy");
                  document.body.removeChild(tempInput);
                

                
                  const copyButton = document.querySelector(".copy-button");
                  copyButton.classList.add("copied");
                  copyButton.innerHTML = "<i class=\"fas fa-check\"></i> 已复制";

                
                  setTimeout(() => {
                    copyButton.classList.remove("copied");
                    copyButton.innerHTML = "<i class=\"fas fa-copy\"></i> 复制";
                  }, 1000);
                }
                const markdownCheckbox = document.getElementById("markdownCheckbox");
                const messageText = document.getElementById("message-text");
                let originalMessage = messageText.innerText;

                markdownCheckbox.addEventListener("change", function() {
                    if (markdownCheckbox.checked) {
                        const renderer = new marked.Renderer();
                        marked.setOptions({
                            renderer: renderer,
                            breaks: true,
                            gfm: true
                        });
                        messageText.innerHTML = marked.parse(originalMessage);
                        renderMathInElement(messageText, {
                            delimiters: [
                                {left: "$$", right: "$$", display: true},
                                {left: "$", right: "$", display: false},
                                {left: "\\\\[", right: "\\\\]", display: true},
                                {left: "\\\\(", right: "\\\\)", display: false}
                            ],
                            throwOnError: false,
                            output: "html"
                        });
                    } else {
                        messageText.innerHTML = originalMessage; 
                    }
                });
                </script>';
                        echo '<script>
                            document.getElementById("autoHeightCheckbox").addEventListener("change", function() {
                                const contentBox = document.getElementById("message-text");
                                if (this.checked) {
                                    contentBox.classList.add("auto-height");
                                } else {
                                    contentBox.classList.remove("auto-height");
                                }
                            });

                            document.getElementById("markdownCheckbox").addEventListener("change", function() {
                                const markdownContent = document.getElementById("markdownContent");
                                const renderedContent = document.getElementById("renderedContent");
                                if (this.checked && markdownContent) {
                                    const renderer = new marked.Renderer();
                                    marked.setOptions({
                                        renderer: renderer,
                                        breaks: true,
                                        gfm: true
                                    });
                                    renderedContent.innerHTML = marked.parse(markdownContent.innerText);
                                    renderMathInElement(renderedContent, {
                                        delimiters: [
                                            {left: "$$", right: "$$", display: true},
                                            {left: "$", right: "$", display: false},
                                            {left: "\\\\[", right: "\\\\]", display: true},
                                            {left: "\\\\(", right: "\\\\)", display: false}
                                        ],
                                        throwOnError: false,
                                        output: "html"
                                    });
                                    markdownContent.style.display = "none";
                                    renderedContent.style.display = "block";
                                } else if (markdownContent) {
                                    markdownContent.style.display = "block";
                                    renderedContent.style.display = "none";
                                }
                            });
                        </script>';
                        echo '</div>';
                    } else {
                        echo '<div class="confirmation-box">';
                        echo '<h2>请输入密码</h2>';
                        echo '<p>此消息受密码保护，只能查看一次，查看后将被永久删除。</p>';
                        echo '<form method="POST" action="?file=' . urlencode($_GET['file']) . '&code=' . urlencode($_GET['code']) . '&confirm=1">';
                        echo '<input type="password" name="senderPassword" placeholder="密码" required>';
                        echo '<div class="flex justify-between w-full">
                <button type="submit">确认</button>
                <button type="button" class="back-link" onclick="window.history.back()">返回</button>
                </div>';
                        echo '</form>';
                        echo '</div>';
                    }
                } else {
                    if (!is_null($messageData['senderPasswordHash'])) {
                        echo '<div class="confirmation-box">';
                        echo '<h2>请输入密码</h2>';
                        echo '<p>此消息受密码保护，只能查看一次，查看后将被永久删除。</p>';
                        echo '<form method="POST" action="?file=' . urlencode($_GET['file']) . '&code=' . urlencode($_GET['code']) . '&confirm=1">';
                        echo '<input type="password" name="senderPassword" placeholder="密码" required>';
                        echo '<div class="flex justify-between w-full">
                <button type="submit">确认</button>
                <button type="button" class="back-link" onclick="window.history.back()">返回</button>
                </div>';
                        echo '</form>';
                        echo '</div>';
                    } else {
                        echo '<div class="confirmation-box">';
                        echo '<h2>确认查看消息？</h2>';
                        echo '<p>此消息只能查看一次，查看后将被永久删除。</p>';
                        echo '<form method="POST" action="?file=' . urlencode($_GET['file']) . '&code=' . urlencode($_GET['code']) . '&confirm=1">';
                        echo '<div class="flex justify-between w-full">
                <button type="submit">确认查看</button>
                <button type="button" class="back-link" onclick="window.history.back()">返回</button>
                </div>';
                        echo '</form>';
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
                            <div class="editor-content markdown"></div>
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
                                    class="shadow appearance-none border rounded w-full py-2 pl-3 pr-12 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                    oninput="validateAndUpdateTime()">
                                <span class="absolute right-3 top-2 text-gray-600 text-sm">天</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="relative">
                                <input type="number" id="hours" name="expiry_hours" min="0" max="23" value="0"
                                    class="shadow appearance-none border rounded w-full py-2 pl-3 pr-12 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                    oninput="validateAndUpdateTime()">
                                <span class="absolute right-3 top-2 text-gray-600 text-sm">时</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="relative">
                                <input type="number" id="minutes" name="expiry_minutes" min="0" max="59" value="0"
                                    class="shadow appearance-none border rounded w-full py-2 pl-3 pr-12 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                                    oninput="validateAndUpdateTime()">
                                <span class="absolute right-3 top-2 text-gray-600 text-sm">分</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="relative">
                                <input type="number" id="seconds" name="expiry_seconds" min="0" max="59" value="0"
                                    class="shadow appearance-none border rounded w-full py-2 pl-3 pr-12 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
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

</div>

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
    currentExpiry.textContent = `当前设置：${days}天${hours}时${minutes}分${seconds}秒`;
    
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
    const messageContent = document.getElementById('message').value;
    const previewElement = document.getElementById('previewArea').querySelector('.editor-content');
    previewElement.innerHTML = marked.parse(messageContent);
    renderMathInElement(previewElement, {
        delimiters: [
            {left: "$$", right: "$$", display: true},
            {left: "$", right: "$", display: false}
        ]
    });

    if (document.getElementById('autoHeight').checked) {
        autoResize();
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
