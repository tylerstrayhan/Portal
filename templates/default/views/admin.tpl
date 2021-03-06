<div class="row-fluid">
    <div class="span12">
        <div class="page-header">
            <h1>
                <i class="icon-cogs icon-large"></i> Admin Panel
                <small>Configuration for the web portal and plugin.</small>
            </h1>
        </div>
        {% if staticCall('fRequest', 'isPost') and not checkMessage('input', 'admin') %}
            <div class="alert alert-block alert-success">
                <p>
                    <strong>Success!</strong> Operation successfully executed.
                </p>
            </div>
        {% endif %}
        {{ Util.showMessages('input', 'admin', 'alert alert-block alert-danger') }}
    </div>
</div>
<form method="post" name="settings" id="settings" class="form-setup">
    <div class="row-fluid">
        <div class="span3">
            {% include 'admin/navi.tpl' %}
        </div>
        <div class="span9">
            {% include sub ignore missing %}
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <div class="form-actions">
                <div class="pull-left">
                    {% if sub and not main %}
                        <button class="btn btn-large btn-primary" name="save" value="true" id="Save">
                            <i class="icon-save"></i> Save
                        </button>
                    {% endif %}
                </div>
                <div class="pull-right">
                    <button class="btn btn-large btn-danger" name="logout" value="true">
                        <i class="icon-signout"></i> Logout
                    </button>
                    <a href="./" class="btn btn-large">
                        <i class="icon-home"></i> Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>