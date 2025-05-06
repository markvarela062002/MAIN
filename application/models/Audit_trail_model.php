<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Audit_trail_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('DataTableService');
    }

    public function get_all_audit_trails()
    {
        return $this->db
            ->select('id, user_id, table_name, event, old_values, new_values, url, ip_address, user_agent, created_at, conditions')
            ->from('user_audit_trails');
    }

    public function get_all_dictionary()
    {
        return $this->db
            ->select('table_name, column_name, column_description, data_type, data_width, is_prikey, is_nullable, numeric_precision, numeric_scale')
            ->from('hdata_dict');
    }

    public function get_user_audits_by_id($id)
    {   
        $query = $this->db
            ->select('at.id, at.user_id, at.table_name, at.event, at.old_values, at.new_values, at.url, at.ip_address, at.user_agent, at.created_at, at.conditions')
            ->from('user_audit_trails as at')
            ->where('at.id', $id)
            ->get();
        
        // Debug the SQL query
        log_message('debug', 'SQL: ' . $this->db->last_query());
        
        $result = $query->result();
        
        // Fetch column descriptions for the table in this audit record
        if (!empty($result)) {
            $table_name = $result[0]->table_name;
            
            // Get column mappings (column_name to column_description)
            $column_mappings = $this->get_column_descriptions($table_name);
            
            // Attach the column mappings to the result
            foreach ($result as $row) {
                $row->column_mappings = $column_mappings;
            }
        }
        
        return $result;
    }
    
    /**
     * Get column descriptions for a specific table
     * 
     * @param string $table_name The table name
     * @return array Associative array mapping column_name to column_description
     */
    public function get_column_descriptions($table_name)
    {
        $query = $this->db
            ->select('column_name, column_description')
            ->from('hdata_dict')
            ->where('table_name', $table_name)
            ->get();
        
        $mappings = [];
        foreach ($query->result() as $row) {
            $mappings[$row->column_name] = $row->column_description;
        }
        
        return $mappings;
    }
}