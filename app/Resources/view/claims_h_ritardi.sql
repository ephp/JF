create or replace view claims_h_ritardi as
select p.id, p.cliente_id, p.claimant, p.codice,
       p.gestore_id, g.sigla as gestore, 
       p.priorita_id, sp.priorita, sp.css as priorita_css, 
       p.status, 
       p.stato_pratica_id, sso.stato as stato_pratica, 
       count(*) as n, max(e.data_ora) as ultima_modifica, datediff(now(), max(e.data_ora)) as giorni
  from claims_h_eventi e
 inner join claims_h_pratiche p
       on e.pratica_id = p.id
 inner join cal_tipi t
       on e.tipo_id = t.id
 inner join claims_priorita sp
       on sp.id = p.priorita_id
 inner join claims_stati_pratica sso
       on sso.id = p.stato_pratica_id
 inner join acl_gestori g
       on g.id = p.gestore_id
 
 where (
        t.sigla IN ('OTH', 'CHS')
        OR (
            t.sigla = 'VER'
            AND e.note != ''
		     )
		  )

 group by e.pratica_id
 order by giorni desc