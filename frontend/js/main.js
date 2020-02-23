var addUser = function (id, username, avatar_url, created_at, is_hide) {
    var tr = $("table#users").find('tbody').append($('<tr class="user" data-id="' + id + '">'));
    tr.append($('<td class="id">').text(id));
    tr.append($('<td class="username btn-user-view" data-id="'+id+'">').text(username));
    tr.append($('<td class="avatar">').append($('<img class="small-user-avatar">').attr('src', avatar_url)));
    tr.append($('<td class="created_at">').text(created_at));
};
var clearTable = function () {
    $("table#users").find('tbody').html('');
};
var userHide = function (id, hide) {
    $.ajax({
        url: "http://backend.localhost/hide_user",
        data: {
            id: id,
            hide: hide
        },
        method: 'POST',
        context: document.body
    }).done(function (res) {
        if (res.status !== 'undefined' && res.status.code !== 'undefined' && res.status.text) {
            if (res.status.code === 200) {
                $("#modalUserIsHideChecked").attr('checked', res.result);
            } else {
                alert(res.status.code + ': ' + res.status.text);
            }
        } else {
            alert('Unknown error');
        }
    });
};
var searchUser = function (search, show_hidden) {
    if (search !== '') {
        $.ajax({
            url: "http://backend.localhost/users",
            data: {
                username: search,
                show_hidden: (show_hidden * 1)
            },
            method: 'GET',
            context: document.body
        }).done(function (res) {
            clearTable();
            if (res.status !== 'undefined' && res.status.code !== 'undefined' && res.status.text) {
                if (res.status.code === 200) {
                    res.result.forEach(function (user) {
                        this.addUser(user.id, user.username, user.avatar_url, user.created_at.date + '(' + user.created_at.timezone + ')', user.is_hide)
                    });
                } else {
                    alert(res.status.code + ': ' + res.status.text);
                }
            } else {
                alert('Unknown error');
            }
        });
    }
};

$(document).on('keypress', 'input#search', function (e) {
    if (e.which === 13) {
        searchUser($('input#search').val(), $("#show_hidden_checkbox").is(':checked'));
    }
});

$(document).on('click', ".btn-search", function () {
    searchUser($('input#search').val(), $("#show_hidden_checkbox").is(':checked'));
});
$(document).on('change', "#modalUserIsHideChecked", function () {
    userHide($(this).attr('data-user-id'), $("#modalUserIsHideChecked").is(':checked'));
});

$(document).on('change', "#show_hidden_checkbox", function () {
    searchUser($('input#search').val(), $("#show_hidden_checkbox").is(':checked'));
});

$(document).on('click', ".btn-user-view", function () {
    var id = $(this).attr('data-id');
    $.ajax({
        url: "http://backend.localhost/user",
        data: {
            id: id
        },
        method: 'GET',
        context: document.body
    }).done(function (res) {
        if (res.status !== 'undefined' && res.status.code !== 'undefined' && res.status.text) {
            if (res.status.code === 200) {
                console.log(res.result);
                $('.user-modal-container .id').text(res.result.id);
                $('.user-modal-container .username').text(res.result.username);
                if (res.result.avatar_url !== null) {
                    $('.user-modal-container .avatar img').attr('src', res.result.avatar_url);
                } else {
                    $('.user-modal-container .avatar img').attr('src', '/images/no-person.jpg');
                }
                $('.user-modal-container .created_at').text(res.result.created_at.date + ' (' + res.result.created_at.timezone + ')');
                if (res.result.is_hide) {
                    $('#modalUserIsHideChecked').attr('data-user-id',res.result.id).attr('checked', true);
                } else {
                    $('#modalUserIsHideChecked').attr('data-user-id',res.result.id).attr('checked', false);
                }
                $('#userModal').modal();
            } else {
                alert(res.status.code + ': ' + res.status.text);
            }
        } else {
            alert('Unknown error');
        }
    });

});


// Basic example
$(document).ready(function () {
    $('#users').DataTable({
        "pagingType": "simple_numbers",
        "searching": false,
        "ordering": false,
    });
    $('.dataTables_length').addClass('bs-select');
});
