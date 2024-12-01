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

    if (!empty($siteIcon)) {
        if (strpos($siteIcon, 'data:image') === 0) {
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

        /* Reset default margins */
        .markdown * {
            margin: 0;
            padding: 0;
        }
        
        /* Compact markdown styles */
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
        $message = isset($_POST['message']) ? sanitizeInput($_POST['message']) : '';

        if (empty($message)) {
            echo '<div class="message-box mb-6">';
            echo '<p class="text-red-500">消息不能为空。</p>';
            echo '</div>';
            exit;
        }

        $verificationCode = generateVerificationCode();
        $hashedVerificationCode = hashVerificationCode($verificationCode);
        $hashedSenderPassword = hashPassword($senderPassword);

        $randomKey = generateEncryptionKey();
        $encryptedMessage = encrypt($message, $randomKey);
        $encryptedSenderName = encrypt($senderName, $randomKey);
        $encryptedSenderNote = encrypt($senderNote, $randomKey);

        $keyEncryptedWithVerificationCode = encrypt($randomKey, $hashedVerificationCode);
        $keyEncryptedWithSenderPassword = $hashedSenderPassword ? encrypt($randomKey, $hashedSenderPassword) : null;

        $filename = generateRandomFilename();

        if (!file_exists('messages')) mkdir('messages', 0755, true);

        file_put_contents($filename, json_encode([
            'senderNameEncrypted' => $encryptedSenderName,
            'senderNoteEncrypted' => $encryptedSenderNote,
            'senderPasswordHash' => $hashedSenderPassword,
            'messageEncrypted' => $encryptedMessage,
            'keyEncryptedWithVerificationCode' => $keyEncryptedWithVerificationCode,
            'keyEncryptedWithSenderPassword' => $keyEncryptedWithSenderPassword,
            'hashedVerificationCode' => $hashedVerificationCode
        ]), LOCK_EX);

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
    } else if (isset($_GET['file']) && isset($_GET['code'])) {
        $filename = basename($_GET['file']);
        $verificationCode = sanitizeInput($_GET['code']);

        if (file_exists("messages/$filename")) {
            $messageData = json_decode(file_get_contents("messages/$filename"), true);

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
                    <label for="senderName" class="block text-gray-700 font-bold mb-2">您的名字（可选）:</label>
                    <input type="text" id="senderName" name="senderName"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           placeholder="请输入您的名字">
                </div>
                <div class="mb-4">
                    <label for="senderNote" class="block text-gray-700 font-bold mb-2">备注（可选）:</label>
                    <input type="text" id="senderNote" name="senderNote"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           placeholder="请输入备注">
                </div>
                <div class="mb-4">
                    <label for="senderPassword" class="block text-gray-700 font-bold mb-2">设置密码（可选）:</label>
                    <input type="password" id="senderPassword" name="senderPassword"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           placeholder="请输入密码">
                </div>
                <div class="mb-4">
                    <label for="message" class="block text-gray-700 font-bold mb-2">消息内容:</label>
                    <textarea id="message" name="message"
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                              placeholder="请输入消息内容" rows="5" required></textarea>
                </div>
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">发送</button>
            </form>
        </div>

    <?php } ?>

</div>

</body>

</html>
