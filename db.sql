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
    
    ts_lastdig timestamp DEFAULT CURRENT_TIMESTAMP,
    ts_digfinish timestamp DEFAULT CURRENT_TIMESTAMP,
    
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
    
    ts_lastfound timestamp DEFAULT CURRENT_TIMESTAMP,
    ts_lastprobe timestamp DEFAULT CURRENT_TIMESTAMP,
    ts_probefinish timestamp DEFAULT CURRENT_TIMESTAMP,
    
    primary key (id),
    unique key uk_domain_addr (domain_id, addr),
    key fk_domain_id (domain_id)
#   foreign key fk_domain_id (domain_id) references domains (id) on delete cascade
    
) engine = MEMORY, charset = utf8;

insert into domains (id, domain, testmethod) values (uuid_short(), 'pool.ntp.org', 'ping');
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'bad-test-method.jcloud.sjtu.edu.cn', 'tcping', 66);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'gmail-smtp-in.l.google.com', 'tcping', 25);
insert into domains (id, domain, testmethod, port) values (uuid_short(), 'alt1.gmail-smtp-in.l.google.com', 'tcping', 25);
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'www.yahoo.com', 'https_get', 443, '/');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'www.amazon.com', 'http_get_code', 80, '/');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'www.microsoft.com', 'http_get_code', 80, '/', '200');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'www.google.cn', 'https_get_code', 443, '/', '301');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'www.taobao.com', 'http_get_header_match', 80, '/', 'Strict-Transport-Security');
insert into domains (id, domain, testmethod, port, path, criteria) values (uuid_short(), 'www.baidu.com', 'https_get_content_match', 443, '/', 'STATUS OK');
insert into domains (id, domain, testmethod, port, path) values (uuid_short(), 'ipv6.sjtu.edu.cn', 'https_get', 80,'/');
