CREATE TABLE lists (
    list_id integer DEFAULT nextval(('list_pk_seq'::text)::regclass) NOT NULL,
    group_id integer DEFAULT 0 NOT NULL,
    list_name text,
    list_url text,
    list_archives text,
    list_description text
);


ALTER TABLE public.lists OWNER TO gforge;

--
-- Name: COLUMN lists.list_archives; Type: COMMENT; Schema: public; Owner: gforge
--

COMMENT ON COLUMN lists.list_archives IS 'URL optionnelle des archives de la liste';


--
-- Name: COLUMN lists.list_description; Type: COMMENT; Schema: public; Owner: gforge
--

COMMENT ON COLUMN lists.list_description IS 'description de la liste';


--
-- Name: lists_pkey; Type: CONSTRAINT; Schema: public; Owner: gforge; Tablespace: 
--

ALTER TABLE ONLY lists
    ADD CONSTRAINT lists_pkey PRIMARY KEY (list_id);

