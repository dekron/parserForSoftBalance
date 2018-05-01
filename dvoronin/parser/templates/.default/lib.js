var lParser = {
  /**
   * loading file from input and parse it to json object
   * */
  loadFile: function () {
    var input, file, fr;
    var _this = this;

    if (typeof window.FileReader !== 'function') {
      alert("The file API isn't supported on this browser yet.");
      return;
    }

    input = document.getElementById('fileinput');
    if (!input) {
      alert("Um, couldn't find the fileinput element.");
    }
    else if (!input.files) {
      alert("This browser doesn't seem to support the `files` property of file inputs.");
    }
    else if (!input.files[0]) {
      alert("Please select a file before clicking 'Load'");
    }
    else {
      file = input.files[0];
      fr = new FileReader();
      fr.onload = receivedText;
      fr.readAsText(file);
    }

    function receivedText(e) {
      var lines = e.target.result;
      s_data = JSON.parse(lines);
      _this.getStructure();
    }
  },
  /**
   * sending data to bx
   * */
  sendData: function (data, type) {
    return new Promise(function (resolve, reject) {
      BX.ajax({
        url: urlAjax,
        data: {data: data, type: type, SITE_ID: SITE_ID},
        method: 'POST',
        dataType: 'json',
        timeout: 30,
        async: false,
        processData: true,
        scriptsRunFirst: true,
        emulateOnload: true,
        start: true,
        cache: false,
        onsuccess: function (data) {
          resolve(data);
        },
        onfailure: function () {
          reject(data);
        }
      });
    })
  },
  /**
   * get structure from data and send to bx
   * */
  getStructure: function () {
    var _this = this;
    s_settings_users = s_data.settings.users;
    s_settings_rests = s_data.settings.rests;

    s_items = s_data.items;

    var users = [];
    var rests = [];
    var items_ids = [];
    // собираем все типы свойств
    for (var propertyUserName in s_settings_users) {
      users.push(s_settings_users[propertyUserName]);
    }
    // сначала отправляем типы свойств как ИБ
    this.sendData(users, 'setUsers')
      .then(function (value) {
        _this.writeToLog('Загружено типов свойств: ' + users.length);
        _this.writeToLog(users.join(', '));
        _this.scrollLog();

        //собираем все свойства
        for (var propertyRestName in s_settings_rests) {
          rests.push(s_settings_rests[propertyRestName].name);
        }

        // отправляем свойства у типов
        window.setTimeout(function () {
          return _this.sendData({rests: Object.values(s_settings_rests), users: users}, 'setRests')
        }, 100);
      })
      .then(function (value) {
        _this.writeToLog('Загружено свойств: ' + rests.length);
        _this.writeToLog(rests.join(', '));
        _this.scrollLog();

        //собираем id всех элементов
        s_items.forEach(function (item) {
          items_ids.push(item.id);
        });

        _this.writeToLog('Количество элементов для загрузки: ' + items_ids.length);
        _this.writeToLog('IDs: ' + items_ids.join(', '));
        _this.writeToLog('Загрузка элементов...');
        _this.scrollLog();

        //загружаем все элементы
        s_items.forEach(function (item) {
          var props = [];
          //перебираем типы свойств users у item
          for (var propertyUserName in s_settings_users) {
            var prop = {};
            prop.name = s_settings_users[propertyUserName];
            prop.values = [];

            //перебираем свойства у типов свойств
            for (var propertyRestName in s_settings_rests) {
              //устанавливаем дефолтное значение если его нет
              var value = item[propertyUserName] && item[propertyUserName][propertyRestName]
                ? item[propertyUserName][propertyRestName]
                : s_settings_rests[propertyRestName].default;

              prop.values.push({
                name: s_settings_rests[propertyRestName].name,
                value: value
              })
            }
            props.push(prop);
          }
          // отправляем элемент на сервер
          window.setTimeout(function () {
            _this.sendData({id: item.id, props: props}, 'addElements')
              .then(function () {
                window.setTimeout(function () {
                  _this.writeToLog('Элемент загружен: ' + item.id);
                  _this.scrollLog();
                }, 100);
              })
          }, 100);

        });
      })

  },
  /**
   * scrolling log
   * */
  scrollLog: function () {
    window.setTimeout(function () {
      var elem = document.getElementById('parser-form__log');
      elem.scrollTop = elem.scrollHeight;
    }, 500);
  },
  /**
   * write line to log
   * */
  writeToLog: function (text) {
    var log = document.getElementById('parser-form__log');

    var div = document.createElement('div');
    div.innerHTML = text;

    log.appendChild(div);
  }
};