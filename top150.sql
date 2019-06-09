use cdndns;
drop table if exists targets;
drop table if exists domains;

create table domains (

    id bigint unsigned not null,
    domain varchar(256) not null,

    testmethod varchar(256) not null,
    port smallint default null,
    path varchar(1024) default null,
    criteria varchar(1024) default null,

    ts_lastdig timestamp DEFAULT 0,
    ts_digfinish timestamp DEFAULT 0,

    primary key (id),
    unique key uk_domain (domain)

) engine = MEMORY, charset = utf8;

create table targets (

    id bigint unsigned not null,
    domain_id bigint unsigned not null,
    addr varchar(45) not null,

    response bool default false,
    response_pctg float unsigned default 0.0,
    valid bool default false,
    valid_pctg float unsigned default 0.0,
    time_response int unsigned default 4294967295,
    time_valid int unsigned default 4294967295,

    ts_lastfound timestamp DEFAULT 0,
    ts_lastprobe timestamp DEFAULT 0,
    ts_probefinish timestamp DEFAULT 0,

    primary key (id),
    unique key uk_domain_addr (domain_id, addr),
    key fk_domain_id (domain_id)
#   foreign key fk_domain_id (domain_id) references domains (id) on delete cascade

) engine = MEMORY, charset = utf8;

insert into domains (id, domain, testmethod) values (uuid_short(), 'google.com', 'ping');
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'youtube.com', 'tcping', 66);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'facebook.com', 'tcping', 25);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'wikipedia.org', 'tcping', 25);
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'yahoo.com', 'https_get', 443, '/');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'live.com', 'http_get_code', 80, '/');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'yandex.ru', 'http_get_code', 80, '/', '200');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'weibo.com', 'https_get_code', 443, '/', '301');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'instagram.com', 'http_get_header_match', 80, '/', 'Strict-Transport-Security');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'blogspot.com', 'https_get_content_match', 443, '/', 'STATUS OK');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'linkedin.com', 'https_get', 80,'/');
insert into domains (id, domain, testmethod) values (uuid_short(), 'netflix.com', 'ping');
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'mail.ru', 'tcping', 66);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'microsoft.com', 'tcping', 25);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'google.co.in', 'tcping', 25);
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'google.com.hk', 'https_get', 443, '/');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'bing.com', 'http_get_code', 80, '/');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'office.com', 'http_get_code', 80, '/', '200');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'apple.com', 'https_get_code', 443, '/', '301');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'bilibili.com', 'http_get_header_match', 80, '/', 'Strict-Transport-Security');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'whatsapp.com', 'https_get_content_match', 443, '/', 'STATUS OK');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'googleusercontent.com', 'https_get', 80,'/');
insert into domains (id, domain, testmethod) values (uuid_short(), 'google.de', 'ping');
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'google.co.jp', 'tcping', 66);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'xhamster.com', 'tcping', 25);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'google.ru', 'tcping', 25);
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'adobe.com', 'https_get', 443, '/');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'fandom.com', 'http_get_code', 80, '/');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'google.com.br', 'http_get_code', 80, '/', '200');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'google.fr', 'https_get_code', 443, '/', '301');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'dropbox.com', 'http_get_header_match', 80, '/', 'Strict-Transport-Security');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'porn555.com', 'https_get_content_match', 443, '/', 'STATUS OK');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'google.it', 'https_get', 80,'/');
insert into domains (id, domain, testmethod) values (uuid_short(), 'fbcdn.net', 'ping');
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'tumblr.com', 'tcping', 66);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'google.cn', 'tcping', 25);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'google.es', 'tcping', 25);
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'thestartmagazine.com', 'https_get', 443, '/');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'onlinesbi.com', 'http_get_code', 80, '/');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'bbc.com', 'http_get_code', 80, '/', '200');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'google.co.uk', 'https_get_code', 443, '/', '301');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'instructure.com', 'http_get_header_match', 80, '/', 'Strict-Transport-Security');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'cnn.com', 'https_get_content_match', 443, '/', 'STATUS OK');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'bbc.co.uk', 'https_get', 80,'/');
insert into domains (id, domain, testmethod) values (uuid_short(), 'nih.gov', 'ping');
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'google.com.mx', 'tcping', 66);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'mozilla.org', 'tcping', 25);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'chaturbate.com', 'tcping', 25);
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'slideshare.net', 'https_get', 443, '/');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'uol.com.br', 'http_get_code', 80, '/');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'google.com.tr', 'http_get_code', 80, '/', '200');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'txxx.com', 'https_get_code', 443, '/', '301');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'google.com.tw', 'http_get_header_match', 80, '/', 'Strict-Transport-Security');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'openload.co', 'https_get_content_match', 443, '/', 'STATUS OK');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'godaddy.com', 'https_get', 80,'/');
insert into domains (id, domain, testmethod) values (uuid_short(), 'uclaut.net', 'ping');
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'google.ca', 'tcping', 66);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'otvfoco.com.br', 'tcping', 25);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'canva.com', 'tcping', 25);
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'indiatimes.com', 'https_get', 443, '/');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'medium.com', 'http_get_code', 80, '/');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'google.co.kr', 'http_get_code', 80, '/', '200');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'spankbang.com', 'https_get_code', 443, '/', '301');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'google.pl', 'http_get_header_match', 80, '/', 'Strict-Transport-Security');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'blogger.com', 'https_get_content_match', 443, '/', 'STATUS OK');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'google.com.sa', 'https_get', 80,'/');
insert into domains (id, domain, testmethod) values (uuid_short(), 'google.com.ar', 'ping');
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'google.co.id', 'tcping', 66);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'messenger.com', 'tcping', 25);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'okta.com', 'tcping', 25);
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'namnak.com', 'https_get', 443, '/');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'google.co.th', 'http_get_code', 80, '/');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'mega.nz', 'http_get_code', 80, '/', '200');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'google.com.eg', 'https_get_code', 443, '/', '301');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'youm7.com', 'http_get_header_match', 80, '/', 'Strict-Transport-Security');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'ebay-kleinanzeigen.de', 'https_get_content_match', 443, '/', 'STATUS OK');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'thepiratebay.org', 'https_get', 80,'/');
insert into domains (id, domain, testmethod) values (uuid_short(), 'foxnews.com', 'ping');
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'google.com.au', 'tcping', 66);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'speedtest.net', 'tcping', 25);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'udemy.com', 'tcping', 25);
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'chouftv.ma', 'https_get', 443, '/');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'chinadaily.com.cn', 'http_get_code', 80, '/');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'blackboard.com', 'http_get_code', 80, '/', '200');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'gfycat.com', 'https_get_code', 443, '/', '301');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'ladbible.com', 'http_get_header_match', 80, '/', 'Strict-Transport-Security');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'wordreference.com', 'https_get_content_match', 443, '/', 'STATUS OK');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'rt.com', 'https_get', 80,'/');
insert into domains (id, domain, testmethod) values (uuid_short(), 'rutracker.org', 'ping');
insert into domains (id, domain, testmethod, port) values (uuid_short(), '1337x.to', 'tcping', 66);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'oracle.com', 'tcping', 25);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'wikimedia.org', 'tcping', 25);
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'hubspot.com', 'https_get', 443, '/');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'news-speaker.com', 'http_get_code', 80, '/');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'genius.com', 'http_get_code', 80, '/', '200');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'google.com.ua', 'https_get_code', 443, '/', '301');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'usps.com', 'http_get_header_match', 80, '/', 'Strict-Transport-Security');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'pixabay.com', 'https_get_content_match', 443, '/', 'STATUS OK');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'doubleclick.net', 'https_get', 80,'/');
insert into domains (id, domain, testmethod) values (uuid_short(), 'kinopoisk.ru', 'ping');
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'exoclick.com', 'tcping', 66);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'patreon.com', 'tcping', 25);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'hespress.com', 'tcping', 25);
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'sarkariresult.com', 'https_get', 443, '/');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'xfinity.com', 'http_get_code', 80, '/');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'fedex.com', 'http_get_code', 80, '/', '200');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'dkn.tv', 'https_get_code', 443, '/', '301');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'webex.com', 'http_get_header_match', 80, '/', 'Strict-Transport-Security');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'aol.com', 'https_get_content_match', 443, '/', 'STATUS OK');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'googlevideo.com', 'https_get', 80,'/');
insert into domains (id, domain, testmethod) values (uuid_short(), 'hdfcbank.com', 'ping');
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'google.gr', 'tcping', 66);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'mit.edu', 'tcping', 25);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'ilovepdf.com', 'tcping', 25);
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'telegram.org', 'https_get', 443, '/');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'ouo.io', 'http_get_code', 80, '/');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'sex.com', 'http_get_code', 80, '/', '200');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'asos.com', 'https_get_code', 443, '/', '301');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'investing.com', 'http_get_header_match', 80, '/', 'Strict-Transport-Security');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'popcash.net', 'https_get_content_match', 443, '/', 'STATUS OK');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'flickr.com', 'https_get', 80,'/');
insert into domains (id, domain, testmethod) values (uuid_short(), 'jrj.com.cn', 'ping');
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'playstation.com', 'tcping', 66);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'indoxxi.bz', 'tcping', 25);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'okdiario.com', 'tcping', 25);
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'google.com.pk', 'https_get', 443, '/');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'yandex.kz', 'http_get_code', 80, '/');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'google.co.ve', 'http_get_code', 80, '/', '200');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'google.com.vn', 'https_get_code', 443, '/', '301');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'wiktionary.org', 'http_get_header_match', 80, '/', 'Strict-Transport-Security');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'free.fr', 'https_get_content_match', 443, '/', 'STATUS OK');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'huawei.com', 'https_get', 80,'/');
insert into domains (id, domain, testmethod) values (uuid_short(), 'spotscenered.info', 'ping');
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'theepochtimes.com', 'tcping', 66);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'healthline.com', 'tcping', 25);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'ensonhaber.com', 'tcping', 25);
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'geeksforgeeks.org', 'https_get', 443, '/');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'patch.com', 'http_get_code', 80, '/');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'billdesk.com', 'http_get_code', 80, '/', '200');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'khanacademy.org', 'https_get_code', 443, '/', '301');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'youku.com', 'http_get_header_match', 80, '/', 'Strict-Transport-Security');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'hrahdmon.com', 'https_get_content_match', 443, '/', 'STATUS OK');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'google.cl', 'https_get', 80,'/');
insert into domains (id, domain, testmethod) values (uuid_short(), 'albawabhnews.com', 'ping');
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'doublepimpssl.com', 'tcping', 66);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'browsergames2019.com', 'tcping', 25);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'termometropolitico.it', 'tcping', 25);
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'google.co.za', 'https_get', 443, '/');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'jf71qh5v14.com', 'http_get_code', 80, '/');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'elmwatin.com', 'http_get_code', 80, '/', '200');
