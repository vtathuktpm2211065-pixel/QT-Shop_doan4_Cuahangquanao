<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'QT SHOP')</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <!-- Thêm các CSS khác của bạn ở đây -->
</head>
<body>
    <!-- Nội dung chính -->
    @yield('content')
    
    <!-- AI Chatbox -->
    <div class="ai-chatbox">
      <img
        src="https://bot.mygpt.vn/mygpt-chat-icon.png"
        alt="AI Chat"
        class="ai-chat-icon"
        id="aiChatIcon"
      />

      <div class="ai-chat-modal" id="aiChatModal">
        <div class="ai-chat-header">
          <h3><i class="fas fa-robot"></i> Trợ lý AI MyGPT</h3>
          <button class="close-chat" id="closeChat">&times;</button>
        </div>
        <div class="ai-chat-body" id="chatBody">
          <div class="message ai-message">
            <div class="message-bubble">
              Xin chào! Tôi là trợ lý AI MyGPT. Tôi có thể giúp gì cho bạn hôm nay?
            </div>
          </div>
        </div>
        <div class="model-info">Model: GPT-4 Turbo | Phiên bản: 2.1.0</div>
        <div class="ai-chat-footer">
          <input
            type="text"
            id="userInput"
            placeholder="Nhập câu hỏi của bạn..."
          />
          <button id="sendMessage"><i class="fas fa-paper-plane"></i></button>
        </div>
      </div>
    </div>

    <style>
      /* AI Chatbox Styles */
      .ai-chatbox {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
      }

      .ai-chat-icon {
        width: 64px;
        height: 74px;
        cursor: pointer;
        transition: transform 0.3s;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
      }

      .ai-chat-icon:hover {
        transform: scale(1.1);
      }

      .ai-chat-modal {
        position: fixed;
        bottom: 100px;
        right: 20px;
        width: 350px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        z-index: 1001;
        display: none;
        overflow: hidden;
        animation: slideUp 0.3s ease-out;
      }

      @keyframes slideUp {
        from {
          opacity: 0;
          transform: translateY(20px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      .ai-chat-header {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
      }

      .ai-chat-header h3 {
        margin: 0;
        font-size: 18px;
        display: flex;
        align-items: center;
      }

      .ai-chat-header h3 i {
        margin-right: 10px;
      }

      .close-chat {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
      }

      .ai-chat-body {
        padding: 20px;
        height: 300px;
        overflow-y: auto;
        background: #f8f9fa;
      }

      .message {
        margin-bottom: 15px;
        display: flex;
      }

      .user-message {
        justify-content: flex-end;
      }

      .ai-message {
        justify-content: flex-start;
      }

      .message-bubble {
        max-width: 80%;
        padding: 12px 15px;
        border-radius: 18px;
        font-size: 14px;
      }

      .user-message .message-bubble {
        background: #2575fc;
        color: white;
        border-bottom-right-radius: 5px;
      }

      .ai-message .message-bubble {
        background: white;
        color: #333;
        border: 1px solid #e0e0e0;
        border-bottom-left-radius: 5px;
      }

      .ai-chat-footer {
        padding: 15px;
        border-top: 1px solid #e0e0e0;
        display: flex;
      }

      .ai-chat-footer input {
        flex: 1;
        padding: 12px 15px;
        border: 1px solid #e0e0e0;
        border-radius: 30px;
        outline: none;
        font-size: 14px;
      }

      .ai-chat-footer button {
        background: #2575fc;
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-left: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .model-info {
        font-size: 12px;
        color: #7f8c8d;
        text-align: center;
        margin-top: 10px;
      }

      @media (max-width: 768px) {
        .ai-chat-modal {
          width: 300px;
          right: 10px;
        }
      }
    </style>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const chatIcon = document.getElementById("aiChatIcon");
        const chatModal = document.getElementById("aiChatModal");
        const closeChat = document.getElementById("closeChat");
        const sendButton = document.getElementById("sendMessage");
        const userInput = document.getElementById("userInput");
        const chatBody = document.getElementById("chatBody");

        // Mở chat modal
        chatIcon.addEventListener("click", function () {
          chatModal.style.display = "block";
        });

        // Đóng chat modal
        closeChat.addEventListener("click", function () {
          chatModal.style.display = "none";
        });

        // Gửi tin nhắn
        function sendMessage() {
          const message = userInput.value.trim();
          if (message === "") return;

          // Thêm tin nhắn của người dùng
          const userMessageDiv = document.createElement("div");
          userMessageDiv.className = "message user-message";
          userMessageDiv.innerHTML = `<div class="message-bubble">${message}</div>`;
          chatBody.appendChild(userMessageDiv);

          // Xóa input
          userInput.value = "";

          // Cuộn xuống dưới cùng
          chatBody.scrollTop = chatBody.scrollHeight;

          // Giả lập phản hồi từ AI sau 1 giây
          setTimeout(function () {
            const aiMessageDiv = document.createElement("div");
            aiMessageDiv.className = "message ai-message";

            let response =
              "Tôi xin lỗi, tôi chưa được kết nối với hệ thống AI. Đây chỉ là một bản demo giao diện.";

            if (
              message.toLowerCase().includes("xin chào") ||
              message.toLowerCase().includes("hello")
            ) {
              response =
                "Xin chào! Rất vui được gặp bạn. Tôi có thể giúp gì cho bạn?";
            } else if (
              message.toLowerCase().includes("giá") ||
              message.toLowerCase().includes("cost")
            ) {
              response =
                "Hiện tại chúng tôi có nhiều gói dịch vụ với mức giá khác nhau. Bạn có thể truy cập trang giá cả để xem chi tiết.";
            } else if (
              message.toLowerCase().includes("cảm ơn") ||
              message.toLowerCase().includes("thank")
            ) {
              response =
                "Không có gì! Rất vui được giúp đỡ bạn. Nếu bạn có thêm câu hỏi nào, đừng ngần ngại hỏi tôi nhé!";
            }

            aiMessageDiv.innerHTML = `<div class="message-bubble">${response}</div>`;
            chatBody.appendChild(aiMessageDiv);

            // Cuộn xuống dưới cùng
            chatBody.scrollTop = chatBody.scrollHeight;
          }, 1000);
        }

        // Gửi tin nhắn khi nhấn nút
        sendButton.addEventListener("click", sendMessage);

        // Gửi tin nhắn khi nhấn Enter
        userInput.addEventListener("keypress", function (e) {
          if (e.key === "Enter") {
            sendMessage();
          }
        });
      });
    </script>
    
    <!-- Các script khác của bạn -->
    @yield('scripts')
</body>
</html>