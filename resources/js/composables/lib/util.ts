function Util () {};

Util.hasClass = function(el: HTMLElement, className: string) {
  return el.classList.contains(className);
};

Util.addClass = function(el: HTMLElement, className: string) {
  var classList = className.split(' ');
  el.classList.add(classList[0]);
  if (classList.length > 1) Util.addClass(el, classList.slice(1).join(' '));
};

Util.removeClass = function(el: HTMLElement, className: string) {
  var classList = className.split(' ');
  el.classList.remove(classList[0]);
  if (classList.length > 1) Util.removeClass(el, classList.slice(1).join(' '));
};

Util.toggleClass = function(el: HTMLElement, className: string, bool: boolean) {
  if(bool) Util.addClass(el, className);
  else Util.removeClass(el, className);
};

Util.getIndexInArray = function(array: Array<number>, el: number) {
  return Array.prototype.indexOf.call(array, el);
};


Util.cssSupports = function(property: string, value: string) {
  return CSS.supports(property, value);
};

Util.setAttributes = function(el: HTMLElement, attrs: any) {
  for(var key in attrs) {
    el.setAttribute(key, attrs[key]);
  }
};

Util.moveFocus = function (element: HTMLElement) {
  if( !element ) element = document.getElementsByTagName('body')[0];
  element.focus();
  if (document.activeElement !== element) {
    element.setAttribute('tabindex','-1');
    element.focus();
  }
};

Util.extend = function() {
  var extended = {} as any;
  var deep = false;
  var i = 0;
  var length = arguments.length;

  if ( Object.prototype.toString.call( arguments[0] ) === '[object Boolean]' ) {
    deep = arguments[0];
    i++;
  }

  var merge = function (obj: any) {
    for ( var prop in obj ) {
      if ( Object.prototype.hasOwnProperty.call( obj, prop ) ) {
        if ( deep && Object.prototype.toString.call(obj[prop]) === '[object Object]' ) {
          extended[prop] = extend( true, extended[prop], obj[prop] );
        } else {
          extended[prop] = obj[prop];
        }
      }
    }
  };

  for ( ; i < length; i++ ) {
    var obj = arguments[i];
    merge(obj);
  }

  return extended;
};



export default Util;
