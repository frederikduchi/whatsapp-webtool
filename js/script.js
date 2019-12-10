{
  const init = () => {
    document.documentElement.classList.add(`has-js`);

    // start uploading a zip file when a file is selected
    const $uploadFile = document.querySelector(`.upload-file`);
    if ($uploadFile) {
      $uploadFile.addEventListener(`change`, e => {
        const $uploadForm = document.querySelector(`.upload-form`);
        $uploadForm.submit();
        document.querySelector(`.upload-status`).classList.remove(`hidden`);
      });
    }

  };

  init();
}
