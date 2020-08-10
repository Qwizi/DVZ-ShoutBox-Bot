
var dvz_shoutbox_bot = {
    init: (uid) => {
        console.log(uid);
        const sbTextList = document.querySelectorAll("#shoutbox .user ~ .text");
        const regex = "\\[(uid)\\=([0-9]+)\\]";

        for (let i = 0; i < sbTextList.length; i++) {
            const match = sbTextList[i].textContent.match(regex);
            if (match) {
                const targetId = match[2];
                if (uid == targetId) {
                    sbTextList[i].textContent = sbTextList[i].textContent.replace(`[uid=${uid}]`, '');
                } else {
                    sbTextList[i].parentElement.remove();
                }
            }
        }

        dvz_shoutbox.callbacks['update'].push(() => {
            const sbText = document.querySelector("#shoutbox .user ~ .text");
            const match = sbText.textContent.match(regex);
            if (match) {
                const targetId = match[2];
                if (uid == targetId) {
                    sbText.textContent = sbText.textContent.replace(`[uid=${uid}]`, '');
                } else {
                    sbText.parentElement.remove();
                }
            }
        });
    }
}

/*(function() {
    const textNodeList = document.querySelectorAll("#shoutbox .user ~ .text");
    const uid = "{$mybb->user['uid']}";
    console.log(uid);
    Array.prototype.find.call(textNodeList, function(child) {
        const search = child.textContent.search('\[uid\=[0-9]+\]');
        if (search !== -1) {
            const regex = "\\[(uid)\\=([0-9]+)\\]";
            const match = child.textContent.match(regex);
            const childUid = match[2];
            if (childUid == uid) {
                child.textContent = child.textContent.replace("[uid="+uid+"]", "");
                console.log(child.textContent);
            } else {
                child.parentElement.remove();
            }
        }
    });

    dvz_shoutbox.callbacks['update'].push(function() {
        const textNode = document.querySelector("#shoutbox .user ~ .text");
        const search = textNode.textContent.search('\[uid\=[0-9]+\]');
        if (search !== -1) {
            const regex = "\\[(uid)\\=([0-9]+)\\]";
            const match = textNode.textContent.match(regex);
            const childUid = match[2];
            if (childUid == uid) {
                textNode.textContent = textNode.textContent.replace("[uid="+uid+"]", "");
            } else {
                textNode.parentElement.remove();
            }
        }
    });
})*/