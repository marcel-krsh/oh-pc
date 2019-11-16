 var header = {
        Config: {
            DevcoHost: 'https://devco.ohiohome.org',
            AllitaHost: 'https://pcinspectdev.ohiohome.org',
            Logo: 'https://devco.ohiohome.org/AuthorityOnlineALT/Unified/devco_logo_reversed.png',
            LogoAlt: 'Site Logo',
            Css: 'https://devco.ohiohome.org/AuthorityOnlineALT/unified/unified-header.css',
            LeftItems: '',
            RightItems: '',
            UserID: '9176',
            UserInitials: 'AA',
            UserName: 'AmeliaAtchinson (OSM Test)',
            UserToken: 'c3VlejVCVWJOMkxRZzJiMWtmczZkTlpFbHQwdHQ1UzhDMWhvR2NZZFcvYjViYmFOOVBkamZ0L1Y0Q3VjWm1lOFR6Rmd3eUFVSVBqNVBLTDc4MnNnWkE9PTo6Ojo3ZGNmZWIzMi1mMTAxLTRiZGItYTEzMy05ZTdkYzk0MWFhODM6Ojo6NzQuMjAzLjcyLjg0Ojo6Ok1vemlsbGEvNS4wIChNYWNpbnRvc2g7IEludGVsIE1hYyBPUyBYIDEwXzEyXzYpIEFwcGxlV2ViS2l0LzUzNy4zNiAoS0hUTUwsIGxpa2UgR2Vja28pIENocm9tZS83MS4wLjM1NzguOTggU2FmYXJpLzUzNy4zNg==',
        },
        Init: function() {
            this._OverlayConfigValues();
            this._Render();
            this._AttachEvents();
        },
        _OverlayConfigValues: function() {
            if(typeof _uh !== 'undefined') {
                var index;
                for (index = 0; index < _uh.length; ++index) {
                    this.Config[_uh[index][0]] = _uh[index][1];
                }
            }
        },
        _RenderHtml: function() {
            var html = "";
            html = "" +
                "<div id='apcsv-ul-bar'>" +
                "  <div id='apcsv-logo'><img src='"+this.Config.Logo+"' alt='"+this.Config.LogoAlt+"'></div>" +
                "  <div id='apcsv-list-left'>"+this.Config.LeftItems+"</div>" +
                "  <div id='apcsv-avatar' title='"+this.Config.UserName+"' onclick='if(window.current_site=\"allita_pc\"){openUserPreferences();}'>"+this.Config.UserInitials+"</div>" +
                "  <div id='apcsv-menu-icon' class='hvr-grow'><a id='apcsv-toggle' class='pcsv-toggle' onclick='return false;' href='#apcsv-menu-items'>APPS</a>" +
                "    <div id='apcsv-menu-items' class='hidden'>" +
                "      <div class='apcsv-menu-item'> <a href='"+this.Config.DevcoHost+"/AuthorityOnlineALT/'>DEV|CO Compliance</a></div>" +
                "      <div class='apcsv-menu-item'> <a href='"+this.Config.AllitaHost+"/unified_login?user_id="+this.Config.UserID+"&token="+this.Config.UserToken+"'>DEV|CO Inspection</a></div>" +
                "    </div>" +
                "  </div>" +
                "  <div id='apcsv-list-right'>"+this.Config.RightItems+"</div>" +
                "</div>";
            return html;
        },
        _RenderCss: function() {
            return '<link rel="stylesheet" href="'+this.Config.Css+'">';
        },
        _Render: function() {
            var rendered = '';
            rendered += this._RenderHtml();
            rendered += this._RenderCss();
            rendered = '<div id="ohfa-universal-header">' +rendered + '</div>';
            var form = document.getElementById('aspnetForm');
            if(form != null)
                form.innerHTML = form.innerHTML + rendered;
            else
                document.body.innerHTML = rendered + document.body.innerHTML;
        },
        _AttachEvents: function() {
            document.getElementById("apcsv-toggle").addEventListener("click", function( event ) {
                header.ToggleMenu();
            }, false);
        },
        ToggleMenu: function() {
            var menu = document.getElementById("apcsv-menu-items");
            if ( menu.classList.contains('hidden') ) {
                menu.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
            }
        }
    };

    window.onload = function() {
        header.Init();
    };