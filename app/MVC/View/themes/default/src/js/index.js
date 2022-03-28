Date.prototype.toDateInputValue = (function() {
    var local = new Date(this);
    local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
    return local.toJSON().slice(0,10);
});

Date.prototype.addHours = function(h) {
    this.setTime(this.getTime() + (h*60*60*1000));
    return this;
  }

window.onload = (event) => {
    let dateToday = document.getElementsByClassName("autofill-today");
    for (let i = 0; i < dateToday.length; i++) {
        const element = dateToday[i];
        element.value = new Date().toDateInputValue();
    }
    let dateHuge = document.getElementsByClassName("autofill-huge");
    for (let i = 0; i < dateHuge.length; i++) {
        const element = dateHuge[i];
        element.value = (new Date()).addHours(24*365.25*12).toDateInputValue();
    }

    let delButtons = document.getElementsByClassName('delbutton');
    if (delButtons.length==1) {
        delButtons[0].style.display = 'none';
    }
};