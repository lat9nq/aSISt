CREATE TABLE school (
    school_id INT PRIMARY KEY,
    school_name VARCHAR(30)
);

CREATE TABLE student (
    computing_id VARCHAR(6),
    password VARCHAR(50) NOT NULL,
    first_name VARCHAR(30) NOT NULL,
    middle_name VARCHAR(30),
    last_name VARCHAR(50) NOT NULL,
    date_of_birth date NOT NULL,
    primary_phone INT NOT NULL,
    permanent_home_address VARCHAR(100),
    current_mailing_address VARCHAR(100) NOT NULL,
    year INT,
    career VARCHAR(13) NOT NULL,
    school_id INT,
    PRIMARY KEY (computing_id),
    FOREIGN KEY (school_id) REFERENCES school(school_id)
);

CREATE TABLE department(
    dept_mnemonic VARCHAR(4) PRIMARY KEY,
    name VARCHAR(50)
);

CREATE TABLE instructor (
    computing_id VARCHAR(6),
    password VARCHAR(50) NOT NULL,
    first_name VARCHAR(30) NOT NULL, 
    middle_name VARCHAR(30), -- don't need this
    last_name VARCHAR(50) NOT NULL,
    dept_mnemonic VARCHAR(4),
    PRIMARY KEY (computing_id),
    FOREIGN KEY (dept_mnemonic) REFERENCES department(dept_mnemonic)
);

CREATE TABLE course (
    dept_mnemonic VARCHAR(4),
    course_number INT,
    course_title VARCHAR(100),
    description TEXT,
    units INT,
    school_id INT, -- don't need this
    FOREIGN KEY (dept_mnemonic) REFERENCES department(dept_mnemonic),
    FOREIGN KEY (school_id) REFERENCES school(school_id),
    PRIMARY KEY (course_number, dept_mnemonic)
);

CREATE TABLE building (
    building_id INT PRIMARY KEY,
    building_name VARCHAR(50),
    dept_mnemonic VARCHAR(4),
    FOREIGN KEY(dept_mnemonic) REFERENCES department (dept_mnemonic)
);

CREATE TABLE timeslot(
    time_id INT,
    start_time time,
    end_time time,
    PRIMARY KEY (time_id)
);

CREATE TABLE section(
    section_id INT,
    section_key INT,
    dept_mnemonic VARCHAR(4),
    course_number INT,
    building_id INT,
    room VARCHAR(6),
    section_title VARCHAR(50),
    time_id INT,
    semester VARCHAR(20),
    capacity INT,
    total_students INT,
    days VARCHAR(10),
    description VARCHAR(30),
    status TINYINT,
    PRIMARY KEY (section_key),
    FOREIGN KEY (building_id) REFERENCES building(building_id),
    FOREIGN KEY (time_id) REFERENCES timeslot(time_id),
    FOREIGN KEY (dept_mnemonic, course_number) REFERENCES course(dept_mnemonic, course_number)
);

CREATE TABLE degree_requirements (
    dept_mnemonic VARCHAR(4),
    major_dept_mnemonic VARCHAR(4),
    course_number INT,
    PRIMARY KEY (dept_mnemonic, course_number, major_dept_mnemonic),
    FOREIGN KEY (dept_mnemonic, course_number) REFERENCES course(dept_mnemonic, course_number),
    FOREIGN KEY (major_dept_mnemonic) REFERENCES department(dept_mnemonic)
);

CREATE TABLE advisor (
    student_id VARCHAR(6) NOT NULL, 
    instructor_id VARCHAR(6) NOT NULL, 
    PRIMARY KEY (student_id, instructor_id),
    FOREIGN KEY (student_id) REFERENCES student(computing_id), 
    FOREIGN KEY (instructor_id) REFERENCES instructor(computing_id)
);

CREATE TABLE student_section (
	section_key INT,
    student_id VARCHAR(6),
    waitlist_timestamp datetime,
    status INT NOT NULL,
    grade VARCHAR (2),
    PRIMARY KEY (section_key),
    FOREIGN KEY (section_key) REFERENCES section(section_key),
    FOREIGN KEY (student_id) REFERENCES student(computing_id)
);

CREATE TABLE instructor_section (
    instructor_id VARCHAR(6),
	section_key INT,
    FOREIGN KEY (instructor_id) REFERENCES instructor(computing_id),
    FOREIGN KEY (section_key) REFERENCES section(section_key)
);

CREATE TABLE student_department (
    dept_mnemonic VARCHAR(4),
    computing_id VARCHAR(6),
    major TINYINT NOT NULL,
    PRIMARY KEY(dept_mnemonic, computing_id),
    FOREIGN KEY (dept_mnemonic) REFERENCES department(dept_mnemonic),
    FOREIGN KEY (computing_id) REFERENCES student(computing_id)
);

CREATE TABLE school_requirements (
    school_id INT,
    dept_mnemonic VARCHAR(4),
    course_number INT,
    req_name VARCHAR(30),
    PRIMARY KEY (school_id, dept_mnemonic, course_number),
    FOREIGN KEY (school_id) REFERENCES school(school_id),
    FOREIGN KEY (dept_mnemonic, course_number) REFERENCES course(dept_mnemonic, course_number)
);

CREATE TABLE prerequisites (
    dept_mnemonic VARCHAR(4),
    course_number INT,
    prereq_dept_mnemonic VARCHAR(4),
    prereq_course_number INT,
    minimum_grade VARCHAR(2),
    PRIMARY KEY (dept_mnemonic, course_number, prereq_dept_mnemonic, prereq_course_number),
    FOREIGN KEY (dept_mnemonic, course_number) REFERENCES course(dept_mnemonic, course_number),
    FOREIGN KEY (prereq_dept_mnemonic, prereq_course_number) REFERENCES course(dept_mnemonic, course_number)
);
	