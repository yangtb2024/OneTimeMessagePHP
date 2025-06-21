# OneTimeMessagePHP

[![Stars](https://img.shields.io/github/stars/yangtb2024/OneTimeMessagePHP?style=flat-square)](https://github.com/yangtb2024/OneTimeMessagePHP/stargazers)
[![Forks](https://img.shields.io/github/forks/yangtb2024/OneTimeMessagePHP?style=flat-square)](https://github.com/yangtb2024/OneTimeMessagePHP/network/members)
[![Issues](https://img.shields.io/github/issues/yangtb2024/OneTimeMessagePHP?style=flat-square)](https://github.com/yangtb2024/OneTimeMessagePHP/issues)
[![License](https://img.shields.io/github/license/yangtb2024/OneTimeMessagePHP?style=flat-square)](https://github.com/yangtb2024/OneTimeMessagePHP/blob/main/LICENSE)

English | [中文](#chinese-version)

## Introduction

**OneTimeMessagePHP** is a simple, secure, and self-hostable web application for sharing sensitive information that self-destructs after being read once. It's designed to protect your privacy by ensuring that confidential messages, passwords, or other secrets are not permanently stored or accessible after the intended recipient has viewed them.

Built with PHP, it requires no database and is incredibly easy to deploy on any server with PHP support. It uses strong AES-256-CBC encryption to keep your messages secure.

### Live Demo

You can see a live version of the application here: **[https://ilovelinuxdo.tech](https://ilovelinuxdo.tech)**

## Features

-   **One-Time View**: Messages are permanently deleted from the server after being viewed once.
-   **Strong Encryption**: Uses AES-256-CBC encryption to protect messages.
-   **Password Protection**: Add an optional password for an extra layer of security.
-   **Self-Hosted**: Full control over your data by hosting it on your own server.
-   **Database-Free**: No database is required; messages are stored as encrypted files.
-   **Markdown Support**: Write messages using Markdown for rich text formatting.
-   **LaTeX Support**: Render beautiful mathematical formulas using LaTeX syntax.
-   **Easy Deployment**: Simply clone the repository and configure a single `.env` file.

## Installation

### Prerequisites

-   A web server (like Apache, Nginx, or Caddy)
-   PHP 8.0 or higher
-   `openssl` PHP extension

### Steps

1.  **Clone the Repository**
    Clone this repository to your web server.
    ```bash
    git clone https://github.com/yangtb2024/OneTimeMessagePHP.git
    cd OneTimeMessagePHP
    ```

2.  **Configure Environment Variables**
    Copy the example environment file and edit it.
    ```bash
    cp .env.example .env
    ```
    Now, open the `.env` file and set the following variables:

    -   `ENCRYPTION_KEY`: **This is critical!** Generate a strong, random 32-character string. You can use an online generator or a command-line tool. **Changing this key will make all existing messages unreadable.**
    -   `MESSAGE_EXPIRY`: Set the maximum lifetime for a message before it's automatically deleted, even if unread. The format is `Days:Hours:Minutes:Seconds` (e.g., `7:0:0:0` for 7 days).
    -   `SITE_DOMAIN`: The full URL of your website (e.g., `https://yourdomain.com`). This is used to generate the message links.
    -   `SITE_ICON`: The URL for your site's favicon.

3.  **Set Directory Permissions**
    Ensure your web server has write permissions for the `/messages` directory. This is where encrypted messages are temporarily stored.
    ```bash
    chmod -R 755 messages
    chown -R www-data:www-data messages
    ```
    *(Note: The user/group `www-data` might be different depending on your server configuration, e.g., `apache`, `nginx`)*.

4.  **Configure Your Web Server**
    Point your web server's document root to the `OneTimeMessagePHP` directory. Ensure that direct access to the `/messages` directory and the `.env` file is blocked for security.

## Usage

1.  **Create a Message**: Open the application in your browser, type your message, and optionally set a password.
2.  **Generate Link**: Click the "Send" button. The application will generate a unique, shareable link.
3.  **Share Link**: Copy the link and send it to your intended recipient.
4.  **View and Destroy**: The recipient opens the link to view the message. If a password was set, they must enter it. Once the message is displayed, it is instantly and permanently deleted from the server. The link will no longer work.

## Security Warning

-   **Do NOT commit your `.env` file** to any public repository. It contains your secret encryption key.
-   **Back up your `ENCRYPTION_KEY`**. If you lose it, all messages will be unrecoverable.
-   Ensure your web server is properly configured to **prevent directory listing** and block public access to the `.env` file and the `/messages` directory.

## Contributing

Contributions are welcome! If you have ideas for new features, bug fixes, or improvements, please open an issue or submit a pull request.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Star History

[![Star History Chart](https://api.star-history.com/svg?repos=yangtb2024/OneTimeMessagePHP&type=Date)](https://star-history.com/#yangtb2024/OneTimeMessagePHP&Date)

---
<br>

## <a name="chinese-version"></a>中文版本

## 简介

**OneTimeMessagePHP** 是一个简单、安全、可自托管的Web应用，用于分享“阅后即焚”的敏感信息。它旨在保护您的隐私，确保机密消息、密码或其他秘密在被指定接收者查看后立即自毁，不会被永久存储或再次访问。

项目基于 PHP 构建，无需数据库，可以轻松部署在任何支持 PHP 的服务器上。它使用强大的 AES-256-CBC 加密算法来保护您的消息安全。

### 演示站点

您可以访问此处的演示站点以了解实际效果：**[https://ilovelinuxdo.tech](https://ilovelinuxdo.tech)**

## 功能特性

-   **一次性查看**：消息在被查看一次后，会立即从服务器上永久删除。
-   **强加密**：使用 AES-256-CBC 加密算法保护消息内容。
-   **密码保护**：可以为消息设置一个可选的密码，提供额外的安全保护。
-   **自托管**：将应用托管在您自己的服务器上，完全掌控您的数据。
-   **无需数据库**：消息以加密文件的形式存储，无需配置和维护数据库。
-   **Markdown 支持**：使用 Markdown 语法编写消息，以实现富文本格式。
-   **LaTeX 支持**：支持使用 LaTeX 语法渲染精美的数学公式。
-   **部署简单**：只需克隆代码库并配置一个 `.env` 文件即可。

## 安装部署

### 环境要求

-   Web 服务器（如 Apache, Nginx, Caddy）
-   PHP 8.0 或更高版本
-   `openssl` PHP 扩展

### 部署步骤

1.  **克隆代码库**
    将此代码库克隆到您的服务器上。
    ```bash
    git clone https://github.com/yangtb2024/OneTimeMessagePHP.git
    cd OneTimeMessagePHP
    ```

2.  **配置环境变量**
    复制环境变量示例文件并进行编辑。
    ```bash
    cp .env.example .env
    ```
    然后，打开 `.env` 文件并设置以下变量：

    -   `ENCRYPTION_KEY`：**至关重要！** 请生成一个高强度的、随机的32位字符串。您可以使用在线工具或命令行生成。**一旦更改此密钥，所有已创建的消息都将无法解密。**
    -   `MESSAGE_EXPIRY`：设置消息的最长保留时间（即使未读），超时后将自动删除。格式为 `天:时:分:秒`（例如 `7:0:0:0` 代表7天）。
    -   `SITE_DOMAIN`：您的网站的完整 URL（例如 `https://yourdomain.com`），用于生成消息链接。
    -   `SITE_ICON`：您的网站图标（Favicon）的 URL。

3.  **设置目录权限**
    请确保您的 Web 服务器对 `/messages` 目录有写入权限，加密消息会临时存放在此。
    ```bash
    chmod -R 755 messages
    chown -R www-data:www-data messages
    ```
    *（注意：用户/组 `www-data` 可能因您的服务器配置而异，例如可能是 `apache` 或 `nginx`）*。

4.  **配置 Web 服务器**
    将您的 Web 服务器的网站根目录指向 `OneTimeMessagePHP` 项目目录。为安全起见，请确保已禁止对外访问 `/messages` 目录和 `.env` 文件。

## 使用方法

1.  **创建消息**：在浏览器中打开应用，输入您的消息内容，并可以按需设置密码。
2.  **生成链接**：点击“发送”按钮，应用将生成一个唯一、可分享的链接。
3.  **分享链接**：复制该链接并发送给您的目标接收人。
4.  **查看与销毁**：接收者打开链接即可查看消息。如果设置了密码，则需要输入正确密码。消息一旦被成功查看，就会立即从服务器上永久删除，该链接也会随之失效。

## 安全警告

-   **请勿将 `.env` 文件提交**到任何公共代码仓库中，因为它包含您的加密密钥。
-   **请备份您的 `ENCRYPTION_KEY`**。如果丢失，所有消息都将无法恢复。
-   确保您的 Web 服务器配置正确，**禁止目录列表功能**，并阻止公网访问 `.env` 文件和 `/messages` 目录。

## 贡献代码

欢迎您为本项目做出贡献！如果您有关于新功能、错误修复或改进的想法，请提交一个 Issue 或 Pull Request。

## 许可证

本项目基于 MIT 许可证。详情请参阅 [LICENSE](LICENSE) 文件。

## Star History

[![Star History Chart](https://api.star-history.com/svg?repos=yangtb2024/OneTimeMessagePHP&type=Date)](https://star-history.com/#yangtb2024/OneTimeMessagePHP&Date)
