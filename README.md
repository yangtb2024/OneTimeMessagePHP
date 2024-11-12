### 示例站点

- **演示站点:** 您可以访问 [演示站点](https://ilovelinuxdo.tech) 以了解 OneTimeMessagePHP 的实际效果。

如果有任何问题，欢迎提出，我会尽力改正。

以下简介是AI生成，看看就行了，[这是网页版简介](https://ilovelinuxdo.tech/introduce.html)。

## 阅后即焚应用 - OneTimeMessagePHP

### 一、引言

在这个信息时代，隐私和安全变得越来越重要。我们经常需要分享一些敏感信息，例如密码、银行账户信息或者私人消息，但我们又不希望这些信息被其他人看到或者保存下来。为了解决这个问题，我们可以使用阅后即焚应用。

阅后即焚应用允许您创建只能阅读一次的消息，并在阅读后自动销毁。这可以有效地保护您的隐私和信息安全。

### 二、OneTimeMessagePHP 简介

OneTimeMessagePHP 是一个使用 PHP 构建的简单且安全的阅后即焚消息应用。它旨在提供一种便捷的方式来分享敏感信息，同时确保信息在被阅读后立即销毁，防止未经授权的访问。

**主要特点:**

- **易于使用:** OneTimeMessagePHP 拥有简洁直观的界面，您只需输入消息内容，并可选择设置密码来增强安全性。
- **安全可靠:** OneTimeMessagePHP 采用 AES-256-CBC 加密算法和双重加密机制，对您的消息进行加密存储，确保只有拥有正确密码的人才能解密和阅读消息。
- **开源免费:** OneTimeMessagePHP 是一个开源项目，您可以免费使用、修改和分发它，以满足您的特定需求。

### 三、功能特点

- **创建阅后即焚消息:** 您可以轻松创建只能被阅读一次的消息。一旦消息被读取，它将从服务器上永久删除。
- **密码保护 (可选):** 您可以为消息设置密码，以增加一层安全保障。只有知道密码的人才能解密和阅读消息。
- **发件人姓名和备注信息 (可选):** 您可以选择添加发件人姓名和备注信息，以便接收者了解消息的来源和目的。

### 四、使用方法

1. **部署应用:**
    - 将 OneTimeMessagePHP 的代码上传到您的 Web 服务器。
    - 创建一个 `.env` 文件，并在其中设置必要的环境变量。

2. **创建消息:**
    - 打开 OneTimeMessagePHP 应用的首页。
    - 在文本框中输入您的消息内容。
    - (可选) 设置密码以保护您的消息。
    - (可选) 输入发件人姓名和备注信息。
    - 点击 "创建消息" 按钮。

3. **分享消息:**
    - 应用将生成一个唯一的 URL，该 URL 指向您创建的消息。
    - 将此 URL 分享给您想要发送消息的人。

4. **阅读消息:**
    - 当接收者访问该 URL 时，他们将能够阅读您的消息。
    - 一旦消息被读取，它将被自动销毁，并且该 URL 将失效。

### 五、部署方法

1. **使用 Git 克隆代码库:**
    ```bash
    git clone https://github.com/yangtb2024/OneTimeMessagePHP.git
    cd OneTimeMessagePHP
    ```

2. **配置 `.env` 文件:**
    - 复制 `.env.example` 文件并将其重命名为 `.env`。
    - 使用强随机字符串替换 `ENCRYPTION_KEY` 的值。

3. **设置 Web 服务器:**
    - 将 Web 服务器的文档根目录指向 OneTimeMessagePHP 的 `public` 目录。
    - 确保 `/message` 目录对 Web 服务器具有写入权限。


### 六、安全警告

- **更换密钥导致消息失效:** OneTimeMessagePHP 使用 `.env` 文件中设置的 `ENCRYPTION_KEY` 进行消息加密。如果您更改此密钥，之前创建的所有消息都将无法解密和读取，因为它们使用了旧的密钥进行加密。请谨慎更改此密钥，并确保在更改后通知所有相关方。
- **不要将 `.env` 文件提交到代码仓库:** `.env` 文件包含敏感信息，例如加密密钥。请勿将其提交到版本控制系统（例如 Git）中，以防止密钥泄露。
- **确保服务器安全:** 请确保您的 Web 服务器配置正确，并采取必要的安全措施来防止未经授权的访问。
- **`.env` 文件和 `/message` 目录的可读性:** 请注意，`.env` 文件和 `/message` 目录在服务器上是可读的。虽然消息内容是加密的，但文件名和一些元数据可能会被其他人读取。如果您需要更高的安全性，请考虑使用更安全的存储机制来存储消息。 **但这并不意味着您的信息不安全，因为消息内容本身是加密存储的，只有拥有正确密钥的人才能解密和读取。**

### 八、总结

OneTimeMessagePHP 提供了一种简单而有效的方式来分享敏感信息，同时确保信息安全和隐私。请务必仔细阅读安全警告，并采取必要的措施来保护您的信息。

**希望以上信息对您有所帮助！** 

### 九、.env 文件示例

```env
# .env 文件示例
# 请将 ENCRYPTION_KEY 替换为一个强随机字符串
ENCRYPTION_KEY=example_123456

# 设置站点图标（Favicon）的 URL
# 以下是一些示例图标及其类型：

# 1. Base64 编码的图标
# SITE_ICON=data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADElEQVQI12P4//8/AAX+Av7czFnnAAAAAElFTkSuQmCC

# 2. 远程 URL 图标
# SITE_ICON=https://example.com/favicon.ico

# 3. 服务器路径图标
# SITE_ICON=/path/to/your/favicon.ico

# 请选择一个适合您需求的图标配置项
SITE_ICON=https://example.com/favicon.ico

# 设置站点的域名，用于生成消息链接
# 例如：SITE_DOMAIN=https://ilovelinuxdo.tech
SITE_DOMAIN=https://ilovelinuxdo.tech
```

请根据您的实际需求修改 `.env` 文件中的内容。
