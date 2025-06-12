function alarmConfSave() {
  const settings = {
    popup: document
      .querySelector(`.setting_onoff.popup button span`)
      .innerText.trim(),
    reply: document
      .querySelector(`.setting_onoff.reply button span`)
      .innerText.trim(),
    reReply: document
      .querySelector(`.setting_onoff.reReply button span`)
      .innerText.trim(),
  };

  fetch("setting.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(settings),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert("설정이 저장되었습니다.");
      } else {
        alert("저장에 실패했습니다.");
      }
    })
    .catch((error) => {
      console.error("에러 발생:", error);
      alert("통신 오류가 발생했습니다.");
    });
}

function alarmConfToggle(type) {
  const popupBtn = document.querySelector(`.setting_onoff.popup button`);
  const popupSpan = popupBtn.querySelector("span");
  const replyBtn = document.querySelector(`.setting_onoff.reply button`);
  const replySpan = replyBtn.querySelector("span");
  const reReplyBtn = document.querySelector(`.setting_onoff.reReply button`);
  const reReplySpan = reReplyBtn.querySelector("span");

  if (type === "popup") {
    const popupIsOn = popupBtn.classList.contains("on");

    if (popupIsOn) {
      popupBtn.classList.remove("on");
      popupSpan.innerText = "off";

      replyBtn.classList.remove("on");
      replySpan.innerText = "off";

      reReplyBtn.classList.remove("on");
      reReplySpan.innerText = "off";
    } else {
      popupBtn.classList.add("on");
      popupSpan.innerText = "on";

      replyBtn.classList.add("on");
      replySpan.innerText = "on";

      reReplyBtn.classList.add("on");
      reReplySpan.innerText = "on";
    }
  } else {
    const button = document.querySelector(`.setting_onoff.${type} button`);
    const span = button.querySelector("span");

    if (button.classList.contains("on")) {
      button.classList.remove("on");
      span.innerText = "off";
    } else {
      button.classList.add("on");
      span.innerText = "on";
    }

    const replyIsOn = replyBtn.classList.contains("on");
    const reReplyIsOn = reReplyBtn.classList.contains("on");

    if (replyIsOn || reReplyIsOn) {
      popupBtn.classList.add("on");
      popupSpan.innerText = "on";
    } else {
      popupBtn.classList.remove("on");
      popupSpan.innerText = "off";
    }
  }
}
