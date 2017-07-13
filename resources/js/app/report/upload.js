
var i = 0; // 用来动态生成img,file的id
function addAttachmentToList()
{
    //如果此文档已在附件列表中则不再添加
    if (findAttachment(event.srcElement.value)) return;
    // 动态创建附件信息栏并添加到附件列表中
    var imgSrc = document.createElement('img');
    imgSrc.id = 'img' + i;
    imgSrc.innerHTML = extractFileName(event.srcElement.value);
    imgSrc.src = "#";
    G('attachmentList').appendChild(imgSrc);
}
function selectAttachment()
{
    var file = '<input type="file" style="display:none" onchange="addAttachmentToList();" id="photo'+i+'">';
    document.body.insertAdjacentHTML('beforeEnd', file);
    G('photo'+i).click();
}
function extractFileName(fn)
{
    return fn.substr(fn.lastIndexOf('\\')+1);
}
function findAttachment(fn)
{
    var o = G('attachmentList').getElementsByTagName('imgSrc');
    for(var i=0;i<o.length;i++)
        if (o[i].title == fn) return true;
    return false;
}
function G(id)
{
    return document.getElementById(id);
}