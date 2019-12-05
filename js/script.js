{


  const init = () => {
    console.log(`script init`);

    // start uploading a zip file when a file is selected
    const $uploadFile = document.querySelector(`.zip-upload`);
    if ($uploadFile) {
      $uploadFile.addEventListener(`change`, e => {
        /*const $uploadForm = document.querySelector(`.upload-form`);
        console.log(`file selected`);
        const url = $uploadForm.getAttribute(`action`);
        console.log(url);
        const formData = new FormData();
        formData.append(`conversation-zip`, e.currentTarget.files[0]);

        /*const result = await fetch(url, { method: `POST`, body: formData });
        const data = await result.text();
        console.log(result);
        console.log(data);*/
        /*const xhr = new XMLHttpRequest();
        xhr.addEventListener(`progress`, e => console.log(e));
        xhr.open("POST",url,true);
        //xhr.setRequestHeader("Content-Type","multipart/form-data");
        xhr.send(formData);
        console.log("done");*/

      });
    }

  };

  init();
}
