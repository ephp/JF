create or replace view claims_h_ritardi_verifiche as

SELECT e.id,
       e.pratica_id,
       e.cliente_id,
		 max(e.delta_g) as delta_g,
       max(e.data_ora) as data_ora,
       DATEDIFF(NOW(), max(e.data_ora)) as days
  FROM claims_h_eventi e
  LEFT JOIN cal_tipi t
    ON t.id = e.tipo_id
  LEFT JOIN claims_h_pratiche p
    ON p.id = e.pratica_id
  LEFT JOIN claims_priorita pr
    ON pr.id = p.priorita_id
 WHERE t.sigla IN ('VER', 'ASC')
   AND pr.priorita != 'Chiuso'
 GROUP BY e.pratica_id

        